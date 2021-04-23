<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Analysis\Analyzer;

use JeroenG\Explorer\Domain\Analysis\Analyzer\StandardAnalyzer;
use JeroenG\Explorer\Domain\Analysis\Filter\SynonymFilter;
use PHPUnit\Framework\TestCase;

class StandardAnalyzerTest extends TestCase
{
    public function test_it_can_build_without_any_filters(): void
    {
        $analyzer = new StandardAnalyzer('test');

        $build = $analyzer->build();

        $expected = [
            'tokenizer' => 'standard',
            'filter' => [],
        ];

        self::assertSame($expected, $build);
    }

    public function test_it_accepts_a_build_in_and_custom_filter(): void
    {
        $analyzer = new StandardAnalyzer('test');
        $analyzer->setFilters(['lowercase', new SynonymFilter()]);

        $build = $analyzer->build();

        $expected = [
            'tokenizer' => 'standard',
            'filter' => ['lowercase', 'synonym'],
        ];

        self::assertSame($expected, $build);
    }
}
