<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Aggregations;

use JeroenG\Explorer\Domain\Aggregations\MaxAggregation;
use JeroenG\Explorer\Domain\Aggregations\NestedAggregation;
use JeroenG\Explorer\Domain\Aggregations\TermsAggregation;
use PHPUnit\Framework\TestCase;

class NestedAggregationTest extends TestCase
{
    public function test_it_builds_with_no_aggregations(): void
    {
        $aggregation = new NestedAggregation(':field:');
        self::assertEquals([
            'nested' => [
                'path' => ':field:'
            ],
            'aggs' => [],
        ], $aggregation->build());
    }

    public function test_it_builds_with_one_aggregation(): void
    {
        $aggregation = new NestedAggregation(':field:');
        $max = new MaxAggregation(':field:');
        $aggregation->add('aggr', $max);
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
        ], $aggregation->build());
    }

    public function test_it_builds_with_two_aggregation(): void
    {
        $aggregation = new NestedAggregation(':field:');
        $max = new MaxAggregation(':field:');
        $terms = new TermsAggregation(':field-two:', 42);

        $aggregation->add('max-1', $max);
        $aggregation->add('terms-2', $terms);

        self::assertEquals([
            'nested' => [
                'path' => ':field:'
            ],
            'aggs' => [
                'max-1' => [
                    'max' => [
                        'field' => ':field:'
                    ]
                ],
                'terms-2' => [
                    'terms' => [
                        'field' => ':field-two:',
                         'size' => 42,
                    ]
                ]
            ]
        ], $aggregation->build());
    }
}
