<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Aggregations;

use JeroenG\Explorer\Domain\Aggregations\MaxAggregation;
use JeroenG\Explorer\Domain\Aggregations\NestedAggregation;
use PHPUnit\Framework\TestCase;

class AggregationNestedTest extends TestCase
{
    public function test_it_builds(): void
    {
        $aggr = new NestedAggregation(':field:');
        self::assertEquals([
            'nested' => [
                'path' => ':field:'
            ],
            'aggs' => [],
        ], $aggr->build());
    }

    public function test_it_builds_with_max_aggregation(): void
    {
        $aggr = new NestedAggregation(':field:');
        $max = new MaxAggregation(':field:');
        $aggr->add('aggr', $max);
        self::assertEquals([
            'nested' => [
                'path' => ':field:'
            ],
            'aggs' => [
                'aggr' => [
                    'max' => [
                        'field' => ':field:'
                    ]
                ]
            ]
        ], $aggr->build());
    }
}
