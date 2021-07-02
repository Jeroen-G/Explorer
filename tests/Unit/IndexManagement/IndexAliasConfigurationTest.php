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

        $doNotPrune = IndexAliasConfiguration::create('doNotPrune', null, false);
        self::assertFalse($doNotPrune->shouldOldAliasesBePruned());
    }

    public function test_it_can_create_using_custom_suffix(): void
    {
        $config = IndexAliasConfiguration::create('test', 'new-suffix');
        self::assertEquals('test-new-suffix', $config->getIndexName());
        self::assertEquals('test', $config->getAliasName());
    }
}
