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

    public function test_it_verifies_if_it_is_aliased(): void
    {
        $aliasConfig = IndexAliasConfiguration::create('test', true);

        $notAliasedConfig = IndexConfiguration::create('test-1', [], []);
        $aliasedConfig = IndexConfiguration::create('test-2', [], [], null, $aliasConfig);

        self::assertTrue($aliasedConfig->isAliased());
        self::assertFalse($notAliasedConfig->isAliased());
        self::assertEquals($aliasConfig, $aliasedConfig->getAliasConfiguration());
        self::assertEquals('test', $aliasedConfig->getReadIndexName());
        self::assertEquals('test-write', $aliasedConfig->getWriteIndexName());
        self::assertEquals('test-1', $notAliasedConfig->getWriteIndexName());
        $this->expectException(InvalidArgumentException::class);
        $notAliasedConfig->getAliasConfiguration();
    }
}
