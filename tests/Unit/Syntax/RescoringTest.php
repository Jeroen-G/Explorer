<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Syntax;

use JeroenG\Explorer\Domain\Query\Rescoring;
use JeroenG\Explorer\Domain\Syntax\MatchAll;
use PHPUnit\Framework\TestCase;

class RescoringTest extends TestCase
{
    public function test_it_builds_rescoring_query_with_defaults(): void
    {
        $rescoring = new Rescoring();
        $rescoring->setQuery(new MatchAll());

        $result = $rescoring->build();
        self::assertEquals([
            'window_size' => 10,
            'query' => [
                'score_mode' => 'total',
                'rescore_query' => [
                    'match_all' => (object)[]
                ],
                'query_weight' => 1.0,
                'rescore_query_weight' => 1.0,
            ],
        ], $result);
    }

    public function test_it_builds_rescoring_query_with_properties(): void
    {
        $rescoring = new Rescoring();
        $rescoring->setQuery(new MatchAll());
        $rescoring->setScoreMode(Rescoring::SCORE_MODE_MULTIPLY);
        $rescoring->setQueryWeight(2);
        $rescoring->setRescoreQueryWeight(42);
        $rescoring->setWindowSize(1000);

        $result = $rescoring->build();
        self::assertEquals([
            'window_size' => 1000,
            'query' => [
                'score_mode' => 'multiply',
                'rescore_query' => [
                    'match_all' => (object)[]
                ],
                'query_weight' => 2.0,
                'rescore_query_weight' => 42.0,
            ],
        ], $result);
    }
}
