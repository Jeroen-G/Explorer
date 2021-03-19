<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\IndexManagement;

use JeroenG\Explorer\Domain\IndexManagement\IndexConfiguration;
use JeroenG\Explorer\Infrastructure\IndexManagement\ElasticIndexConfigurationRepository;
use JeroenG\Explorer\Tests\Support\Models\TestModelWithSettings;
use PHPUnit\Framework\TestCase;

class ElasticIndexConfigurationRepositoryTest extends TestCase
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
}
