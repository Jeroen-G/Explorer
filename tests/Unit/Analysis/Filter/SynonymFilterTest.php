<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Analysis\Filter;

use JeroenG\Explorer\Domain\Analysis\Filter\SynonymFilter;
use PHPUnit\Framework\TestCase;

class SynonymFilterTest extends TestCase
{
    public function test_it_can_build_without_synonyms(): void
    {
        $filter = new SynonymFilter();

        $build = $filter->build();

        $expected = [
            'type' => 'synonym',
            'synonyms' => [],
        ];

        self::assertSame($expected, $build);
    }

    public function test_it_builds_with_synonyms(): void
    {
        $synonyms = [
            'a => b',
            'c, d => e',
        ];

        $filter = new SynonymFilter();
        $filter->setSynonyms($synonyms);

        $build = $filter->build();

        $expected = [
            'type' => 'synonym',
            'synonyms' => ['a => b', 'c, d => e'],
        ];

        self::assertSame($expected, $build);
    }

    public function test_all_synonyms_must_be_strings(): void
    {
        $filter = new SynonymFilter();

        $this->expectException(\InvalidArgumentException::class);
        $filter->setSynonyms([42, [], new SynonymFilter()]);
    }
}
