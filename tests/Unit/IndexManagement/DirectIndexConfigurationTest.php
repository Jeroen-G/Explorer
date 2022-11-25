<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\IndexManagement;

use InvalidArgumentException;
use JeroenG\Explorer\Domain\IndexManagement\AliasedIndexConfiguration;
use JeroenG\Explorer\Domain\IndexManagement\IndexAliasConfiguration;
use JeroenG\Explorer\Domain\IndexManagement\DirectIndexConfiguration;
use PHPUnit\Framework\TestCase;

final class DirectIndexConfigurationTest extends TestCase
{
    public function test_it_can_create_a_configuration_with_custom_parameters(): void
    {
        $config = DirectIndexConfiguration::create('test', ['properties' => 'go here'], ['settings' => 'yes please'], 'model', null);

        self::assertSame('test', $config->getName());
        self::assertSame(['properties' => 'go here'], $config->getProperties());
        self::assertSame(['settings' => 'yes please'], $config->getSettings());
        self::assertSame('model', $config->getModel());
    }

    public function test_it_verifies_if_it_is_aliased(): void
    {
        $aliasConfig = IndexAliasConfiguration::create(
            name: 'test',
            pruneOldAliases: true,
        );

        $notAliasedConfig = DirectIndexConfiguration::create(
            name: 'test-1',
            properties: [],
            settings: [],
        );
        $aliasedConfig = AliasedIndexConfiguration::create(
            name: 'test-2',
            aliasConfiguration: $aliasConfig,
            properties: [],
            settings: [],
        );

        self::assertEquals($aliasConfig, $aliasedConfig->getAliasConfiguration());
        self::assertEquals('test', $aliasedConfig->getReadIndexName());
        self::assertEquals('test-write', $aliasedConfig->getWriteIndexName());
        self::assertEquals('test-1', $notAliasedConfig->getWriteIndexName());
    }
}
