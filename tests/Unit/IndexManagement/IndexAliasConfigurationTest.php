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
}
