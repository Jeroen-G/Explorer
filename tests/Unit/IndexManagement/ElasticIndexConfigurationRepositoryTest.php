<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\IndexManagement;

use JeroenG\Explorer\Domain\IndexManagement\IndexConfiguration;
use JeroenG\Explorer\Infrastructure\IndexManagement\ElasticIndexConfigurationRepository;
use JeroenG\Explorer\Tests\Support\Models\TestModelWithSettings;
use PHPUnit\Framework\TestCase;

class ElasticIndexConfigurationRepositoryTest extends TestCase
{
    public function testItCreatesConfigFromArray()
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
        self::assertEquals($indices['a']['properties'], $config->properties());
        self::assertEquals($indices['a']['settings'], $config->settings());
        self::assertEquals('a', $config->name());
    }

    public function testItNormalizesConfig()
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
        self::assertEquals($indices['a']['properties']['fld'], $config->properties()['fld']);
        self::assertEquals([ 'type' => 'integer' ], $config->properties()['other']);

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

        self::assertEquals($expectedObject, $config->properties()['object']);
    }

    public function testItCreatesConfigFromClass()
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
        self::assertEquals($model->mappableAs(), $config->properties());
        self::assertEquals($model->indexSettings(), $config->settings());
        self::assertEquals($model->searchableAs(), $config->name());
    }
}
