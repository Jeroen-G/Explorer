<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\IndexManagement;

use JeroenG\Explorer\Domain\IndexManagement\IndexAliasConfiguration;
use PHPUnit\Framework\TestCase;

final class IndexAliasConfigurationTest extends TestCase
{
    public function test_prune_property(): void
    {
        $doPruneByDefault = IndexAliasConfiguration::create(
            name: 'prune',
            pruneOldAliases: true,
        );
        self::assertTrue($doPruneByDefault->shouldOldAliasesBePruned());

        $doNotPrune = IndexAliasConfiguration::create(
            name: 'prune',
            pruneOldAliases: false,
        );
        self::assertFalse($doNotPrune->shouldOldAliasesBePruned());
    }

    /** @dataProvider aliasProvider */
    public function test_it_can_get_the_different_aliases(string $alias, string $method): void
    {
        $config = IndexAliasConfiguration::create(
            name: 'shipIt',
            pruneOldAliases: false,
        );
        self::assertSame("shipIt-$alias", $config->$method());
    }

    public static function aliasProvider(): \Generator
    {
        yield ['history', 'getHistoryAliasName'];
        yield ['write', 'getWriteAliasName'];
    }
}
