<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\IndexManagement;

use JeroenG\Explorer\Domain\IndexManagement\IndexConfiguration;
use JeroenG\Explorer\Domain\IndexManagement\IndexConfigurationNotFoundException;
use JeroenG\Explorer\Infrastructure\IndexManagement\ElasticIndexConfigurationRepository;
use JeroenG\Explorer\Tests\Support\Models\TestModelWithoutSettings;
use JeroenG\Explorer\Tests\Support\Models\TestModelWithSettings;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class ElasticIndexConfigurationRepositoryTest extends MockeryTestCase
{
    public function test_it_creates_the_config_from_array(): void
    {
        $indices = [
            'a' => [
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
        iterator_to_array($repository->getConfigurations())[0] ?? null;
    }

    /** @dataProvider invalidIndices */
    public function test_it_errors_on_invalid_indices($indices): void
    {
        $repository = new ElasticIndexConfigurationRepository($indices);

        $this->expectException(\RuntimeException::class);
        iterator_to_array($repository->getConfigurations());
    }

    public function invalidIndices(): iterable
    {
        yield [[false]];
        yield [
            [[
                'properties' => [
                    'fld' => 'text'
                ],
            ]],
        ];
        yield [
            [
                'a' => [
                    'properties' => [
                        'fld' => 5
                    ],
                ]
            ],
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

    public function test_it_throws_exception_if_configuration_is_not_found(): void
    {
        $repository = new ElasticIndexConfigurationRepository([]);

        $this->expectException(IndexConfigurationNotFoundException::class);
        $this->expectExceptionMessage('The configuration for index encyclopedia could not be found.');
        $repository->findForIndex('encyclopedia');
    }
}
