<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Syntax\Compound;

use JeroenG\Explorer\Domain\Syntax\Compound\FunctionScore;
use JeroenG\Explorer\Domain\Syntax\Compound\ScoreFunction\ScoreFunction;
use JeroenG\Explorer\Domain\Syntax\Compound\ScoreFunction\ScriptScoreFunction;
use JeroenG\Explorer\Domain\Syntax\MatchAll;
use JeroenG\Explorer\Domain\Syntax\Term;
use PHPUnit\Framework\TestCase;

class FunctionScoreTest extends TestCase
{
    public function test_it_can_build_an_empty_query(): void
    {
        $subject = new FunctionScore();

        $expected = [
            'function_score' => [
                'query' => ['match_all' => (object)[]],
                'boost_mode' => 'multiply',
                'score_mode' => 'multiply'
            ],
        ];

        $query = $subject->build();

        self::assertEquals($expected, $query);
    }

    public function test_it_can_set_minscore(): void
    {
        $subject = new FunctionScore();
        $subject->setMinScore(42);

        $expected = [
            'function_score' => [
                'query' => ['match_all' => (object)[]],
                'boost_mode' => 'multiply',
                'score_mode' => 'multiply',
                'min_score' => 42,
            ],
        ];

        $query = $subject->build();

        self::assertEquals($expected, $query);
    }

    public function test_it_can_set_maxboost(): void
    {
        $subject = new FunctionScore();
        $subject->setMaxBoost(42);

        $expected = [
            'function_score' => [
                'query' => ['match_all' => (object)[]],
                'boost_mode' => 'multiply',
                'score_mode' => 'multiply',
                'max_boost' => 42,
            ],
        ];

        $query = $subject->build();

        self::assertEquals($expected, $query);
    }

    public function test_it_can_build_with_script_score_function(): void
    {
        $subject = new FunctionScore();
        $script = new ScriptScoreFunction();
        $script->setParams(['test' => 5]);
        $script->setSource('testSource');
        $script->setWeight(42);
        $subject->addFunction($script);

        $expected = [
            'function_score' => [
                'query' => ['match_all' => (object)[]],
                'functions' => [
                    [
                        'script_score' => [
                            'script' => [
                                'params' => ['test' => 5 ],
                                'source' => 'testSource'
                            ]
                        ],
                        'weight' => 42
                    ],
                ],
                'boost_mode' => 'multiply',
                'score_mode' => 'multiply'
            ],
        ];

        $query = $subject->build();

        self::assertEquals($expected, $query);
    }

    public function test_it_can_build_with_score_function(): void
    {
        $subject = new FunctionScore();
        $script = new ScoreFunction();
        $script->setFilter(new MatchAll());
        $script->setWeight(42);
        $subject->addFunction($script);

        $expected = [
            'function_score' => [
                'query' => ['match_all' => (object)[]],
                'functions' => [
                    [
                        'filter' => ['match_all' => (object)[]],
                        'weight' => 42
                    ],
                ],
                'boost_mode' => 'multiply',
                'score_mode' => 'multiply'
            ],
        ];

        $query = $subject->build();

        self::assertEquals($expected, $query);
    }

    public function test_it_can_build_with_different_modes(): void
    {
        $subject = new FunctionScore();
        $subject->setBoostMode(FunctionScore::BOOST_MAX);
        $subject->setScoreMode(FunctionScore::SCORE_MIN);

        $expected = [
            'function_score' => [
                'query' => ['match_all' => (object)[]],
                'boost_mode' => 'max',
                'score_mode' => 'min'
            ],
        ];

        $query = $subject->build();

        self::assertEquals($expected, $query);
    }

    public function test_it_can_build_with_different_query(): void
    {
        $term = new Term('test', 'yes');
        $subject = new FunctionScore();
        $subject->setQuery($term);

        $expected = [
            'function_score' => [
                'query' => ['term' => ['test' => [ 'value' => 'yes', 'boost' => 1.0]]],
                'boost_mode' => 'multiply',
                'score_mode' => 'multiply'
            ],
        ];

        $query = $subject->build();

        self::assertEquals($expected, $query);
    }
}
