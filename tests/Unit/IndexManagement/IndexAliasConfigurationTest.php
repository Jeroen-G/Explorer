<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\IndexManagement;

use JeroenG\Explorer\Domain\IndexManagement\IndexAliasConfiguration;
use PHPUnit\Framework\TestCase;

class IndexAliasConfigurationTest extends TestCase
{
    public function test_it_prunes_old_aliases_only_by_default(): void
    {
        $doPruneByDefault = IndexAliasConfiguration::create('prune');
        self::assertTrue($doPruneByDefault->shouldOldAliasesBePruned());

        $doNotPrune = IndexAliasConfiguration::create('doNotPrune', false);
        self::assertFalse($doNotPrune->shouldOldAliasesBePruned());
    }

    /** @dataProvider aliasProvider */
    public function test_it_can_get_the_different_aliases(string $alias, string $method): void
    {
        $config = IndexAliasConfiguration::create('shipIt');
        self::assertSame("shipIt-$alias", $config->$method());
    }

    public function aliasProvider(): \Generator
    {
        yield ['history', 'getHistoryAliasName'];
        yield ['write', 'getWriteAliasName'];
    }
}
