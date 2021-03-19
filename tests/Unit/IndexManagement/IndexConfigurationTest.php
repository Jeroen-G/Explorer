<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\IndexManagement;

use JeroenG\Explorer\Domain\IndexManagement\IndexConfiguration;
use PHPUnit\Framework\TestCase;

class IndexConfigurationTest extends TestCase
{
    public function test_it_can_create_a_configuration_with_custom_parameters(): void
    {
        $config = IndexConfiguration::create('test', ['properties' => 'go here'], ['settings' => 'yes please']);

        self::assertSame('test', $config->getName());
        self::assertSame(['properties' => 'go here'], $config->getProperties());
        self::assertSame(['settings' => 'yes please'], $config->getSettings());
    }

    public function test_it_can_give_the_complete_configuration(): void
    {
        $config = IndexConfiguration::create('test', ['id' => 'keyword'], ['analysis' => []]);

        $expected = [
            'index' => 'test',
            'body' => [
                'settings' => [
                    'analysis' => [],
                ],
                'mappings' => [
                    'properties' => [
                        'id' => 'keyword',
                    ],
                ],
            ],
        ];

        self::assertSame($expected, $config->toArray());
    }
}
