<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\IndexManagement;

use InvalidArgumentException;
use JeroenG\Explorer\Domain\IndexManagement\IndexAliasConfiguration;
use JeroenG\Explorer\Domain\IndexManagement\IndexConfiguration;
use PHPUnit\Framework\TestCase;

class IndexConfigurationTest extends TestCase
{
    public function test_it_can_create_a_configuration_with_custom_parameters(): void
    {
        $config = IndexConfiguration::create('test', ['properties' => 'go here'], ['settings' => 'yes please'], 'model');

        self::assertSame('test', $config->getName());
        self::assertSame(['properties' => 'go here'], $config->getProperties());
        self::assertSame(['settings' => 'yes please'], $config->getSettings());
        self::assertSame('model', $config->getModel());
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

    public function test_it_verifies_if_it_is_aliased(): void
    {
        $aliasConfig = IndexAliasConfiguration::create('test', 'suffix');

        $notAliasedConfig = IndexConfiguration::create('test-1', [], []);
        $aliasedConfig = IndexConfiguration::create('test-2', [], [], null, $aliasConfig);

        self::assertTrue($aliasedConfig->isAliased());
        self::assertFalse($notAliasedConfig->isAliased());
        self::assertEquals($aliasConfig, $aliasedConfig->getAliasConfiguration());
        self::assertEquals('test-suffix', $aliasedConfig->getConfiguredIndexName());
        $this->expectException(InvalidArgumentException::class);
        $notAliasedConfig->getAliasConfiguration();
    }
}
