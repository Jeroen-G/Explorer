<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\IndexManagement;

use JeroenG\Explorer\Domain\IndexManagement\IndexAliasConfiguration;
use JeroenG\Explorer\Domain\IndexManagement\IndexConfiguration;
use JeroenG\Explorer\Domain\IndexManagement\IndexConfigurationNotFoundException;
use JeroenG\Explorer\Infrastructure\IndexManagement\ElasticIndexConfigurationRepository;
use JeroenG\Explorer\Tests\Support\Models\TestModelWithAliased;
use JeroenG\Explorer\Tests\Support\Models\TestModelWithoutSettings;
use JeroenG\Explorer\Tests\Support\Models\TestModelWithSettings;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class ElasticIndexConfigurationRepositoryTest extends MockeryTestCase
{
    public function test_it_creates_the_config_from_array(): void
    {
        $indices = [
            'a' => [
                'aliased' => true,
                'settings' => [ 'test' => true ],
                'properties' => [
                    'fld' => [
                        'type' => 'text',
                        'other' => 'This is a test'
                    ]
                ],
            ],
        ];

        $repository = new ElasticIndexConfigurationRepository($indices);

        /** @var IndexConfiguration $config*/
        $config = iterator_to_array($repository->getConfigurations())[0] ?? null;

        self::assertNotNull($config);
        self::assertInstanceOf(IndexConfiguration::class, $config);
        self::assertEquals($indices['a']['properties'], $config->getProperties());
        self::assertEquals($indices['a']['settings'], $config->getSettings());
        self::assertEquals('a', $config->getName());
    }

    public function test_it_normalizes_the_configuration(): void
    {
        $indices = [
             'a' => [
                'properties' => [
                    'fld' => [
                        'type' => 'text',
                        'other' => 'This is a test'
                    ],
                    'other' => 'integer',
                    'object' => [
                        'id' => 'keyword',
                        'age' => [ 'type' => 'integer' ]
                    ]
                ],
            ],
        ];

        $repository = new ElasticIndexConfigurationRepository($indices);

        /** @var IndexConfiguration $config*/
        $config = iterator_to_array($repository->getConfigurations())[0] ?? null;

        self::assertNotNull($config);
        self::assertInstanceOf(IndexConfiguration::class, $config);
        self::assertEquals($indices['a']['properties']['fld'], $config->getProperties()['fld']);
        self::assertEquals([ 'type' => 'integer' ], $config->getProperties()['other']);

        $expectedObject = [
            'type' => 'nested',
            'properties' => [
                'id' => [
                    'type' => 'keyword',
                ],
                'age' => [
                    'type' => 'integer'
                ]
            ]
        ];

        self::assertEquals($expectedObject, $config->getProperties()['object']);
    }

    public function test_it_can_create_the_configuration_from_a_class(): void
    {
        $indices = [
            TestModelWithSettings::class
        ];

        $model = new TestModelWithSettings();
        $repository = new ElasticIndexConfigurationRepository($indices);

        /** @var IndexConfiguration $config*/
        $config = iterator_to_array($repository->getConfigurations())[0] ?? null;

        self::assertNotNull($config);
        self::assertInstanceOf(IndexConfiguration::class, $config);
        self::assertEquals($model->mappableAs(), $config->getProperties());
        self::assertEquals($model->indexSettings(), $config->getSettings());
        self::assertEquals($model->searchableAs(), $config->getName());
        self::assertEquals(TestModelWithSettings::class, $config->getModel());
        self::assertFalse($config->isAliased());
        self::assertEquals($model->searchableAs(), $config->getReadIndexName());
    }

    public function test_it_can_create_the_configuration_from_a_class_without_settings(): void
    {
        $indices = [
            TestModelWithoutSettings::class
        ];

        $repository = new ElasticIndexConfigurationRepository($indices);

        /** @var IndexConfiguration $config*/
        $config = iterator_to_array($repository->getConfigurations())[0] ?? null;

        self::assertNotNull($config);
        self::assertInstanceOf(IndexConfiguration::class, $config);
        self::assertEquals([], $config->getSettings());
    }

    public function test_it_throws_on_invalid_model(): void
    {
        $indices = [
            self::class
        ];

        $repository = new ElasticIndexConfigurationRepository($indices);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(sprintf('Unable to create index %s, ensure it implements Explored', self::class));
        iterator_to_array($repository->getConfigurations())[0] ?? null;
    }

    /** @dataProvider invalidIndices */
    public function test_it_errors_on_invalid_indices($indices, string $error): void
    {
        $repository = new ElasticIndexConfigurationRepository($indices);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage($error);
        iterator_to_array($repository->getConfigurations());
    }

    public function invalidIndices(): iterable
    {
        yield [
            [false],
            'Unable to create index for "false"',
        ];
        yield [
            [[
                'properties' => [
                    'fld' => 'text'
                ],
            ]],
            'Unable to create index for "array',
        ];
        yield [
            [
                'a' => [
                    'properties' => [
                        'fld' => 5
                    ],
                ]
            ],
            'Unable to determine mapping type: 5',
        ];
    }

    public function test_it_can_find_a_single_index(): void
    {
        $indices = [
            'Encyclopedia' => [
                'settings' => [],
                'properties' => [],
            ],
            'encyclopedia' => [
                'settings' => [ 'test' => true ],
                'properties' => [
                    'fld' => [
                        'type' => 'text',
                        'other' => 'This is a test'
                    ]
                ],
            ],
        ];

        $repository = new ElasticIndexConfigurationRepository($indices);

        $config = $repository->findForIndex('encyclopedia');

        self::assertNotNull($config);
        self::assertEquals($indices['encyclopedia']['properties'], $config->getProperties());
        self::assertEquals($indices['encyclopedia']['settings'], $config->getSettings());
        self::assertEquals('encyclopedia', $config->getName());
    }

    public function test_it_can_find_a_single_index_when_it_has_a_prefix(): void
    {
        $indices = [
            'Encyclopedia' => [
                'settings' => [],
                'properties' => [],
            ],
            'encyclopedia' => [
                'settings' => [ 'test' => true ],
                'properties' => [
                    'fld' => [
                        'type' => 'text',
                        'other' => 'This is a test'
                    ]
                ],
            ],
        ];

        $repository = new ElasticIndexConfigurationRepository(indexConfigurations: $indices, scoutPrefix: 'my-prefix-');

        $config = $repository->findForIndex('my-prefix-encyclopedia');

        self::assertNotNull($config);
        self::assertEquals($indices['encyclopedia']['properties'], $config->getProperties());
        self::assertEquals($indices['encyclopedia']['settings'], $config->getSettings());
        self::assertEquals('encyclopedia', $config->getName());
    }

    public function test_it_throws_exception_if_configuration_is_not_found(): void
    {
        $repository = new ElasticIndexConfigurationRepository([]);

        $this->expectException(IndexConfigurationNotFoundException::class);
        $this->expectExceptionMessage('The configuration for index encyclopedia could not be found.');
        $repository->findForIndex('encyclopedia');
    }

    public function test_it_sets_alias_from_aliased_model(): void
    {
        $indices = [TestModelWithAliased::class];

        $repository = new ElasticIndexConfigurationRepository($indices);

        $config = $repository->findForIndex(':searchable_as:');

        self::assertTrue($config->isAliased());
        self::assertTrue($config->getAliasConfiguration()->shouldOldAliasesBePruned());
    }

    public function test_it_throws_exception_when_index_has_no_alias(): void
    {
        $indices = ['encyclopedia' => ['settings' => [], 'properties' => []]];
        $repository = new ElasticIndexConfigurationRepository($indices);
        $config = $repository->findForIndex('encyclopedia');

        $this->expectException(\InvalidArgumentException::class);
        $config->getAliasConfiguration();
    }

    public function test_it_has_pruning_for_aliased_indices_by_default(): void
    {
        $indices = ['encyclopedia' => ['aliased' => true, 'settings' => [], 'properties' => []]];
        $repository = new ElasticIndexConfigurationRepository($indices);
        $config = $repository->findForIndex('encyclopedia');
        self::assertInstanceOf(IndexAliasConfiguration::class, $config->getAliasConfiguration());
        self::assertTrue($config->getAliasConfiguration()->shouldOldAliasesBePruned());
    }

    public function test_it_can_turn_off_pruning_for_aliased_indices(): void
    {
        $indices = ['encyclopedia' => ['aliased' => true, 'settings' => [], 'properties' => []]];
        $repository = new ElasticIndexConfigurationRepository($indices, false);
        $config = $repository->findForIndex('encyclopedia');
        self::assertInstanceOf(IndexAliasConfiguration::class, $config->getAliasConfiguration());
        self::assertFalse($config->getAliasConfiguration()->shouldOldAliasesBePruned());
    }
}
