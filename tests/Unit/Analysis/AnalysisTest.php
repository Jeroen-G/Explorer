<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Analysis;

use JeroenG\Explorer\Domain\Analysis\Analysis;
use JeroenG\Explorer\Domain\Analysis\Analyzer\StandardAnalyzer;
use JeroenG\Explorer\Domain\Analysis\Filter\SynonymFilter;
use PHPUnit\Framework\TestCase;

class AnalysisTest extends TestCase
{
    public function test_an_empty_analysis_is_valid(): void
    {
        $analysis = new Analysis();

        $build = $analysis->build();

        $expected = [
            'analysis' => [
                'analyzer' => [],
                'filter' => [],
            ],
        ];

        self::assertSame($expected, $build);
    }

    public function test_filters_and_analyzers_are_included_in_build(): void
    {
        $analysis = new Analysis();
        $analysis->addAnalyzer(new StandardAnalyzer('synonym'));
        $analysis->addFilter(new SynonymFilter());

        $build = $analysis->build();

        $expected = [
            'analysis' => [
                'analyzer' => [
                    'synonym' => [
                        'tokenizer' => 'standard',
                        'filter' => [],
                    ]
                ],
                'filter' => [
                    'synonym' => [
                        'type' => 'synonym',
                        'synonyms' => [],
                    ]
                ],
            ],
        ];

        self::assertSame($expected, $build);
    }
}
