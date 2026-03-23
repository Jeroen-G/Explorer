<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Aggregations;

use JeroenG\Explorer\Domain\Aggregations\TermsAggregation;
use PHPUnit\Framework\TestCase;

class TermsAggregationTest extends TestCase
{
    public function test_it_builds(): void
    {
        $aggr = new TermsAggregation(':field:');
        self::assertEquals([
            'terms' => [
                'field' => ':field:',
                'size' => 10
            ]
        ], $aggr->build());
    }

    public function test_it_builds_with_size(): void
    {
        $aggr = new TermsAggregation(':field:', 100);
        self::assertEquals([
            'terms' => [
                'field' => ':field:',
                'size' => 100
            ]
        ], $aggr->build());
    }

    public function test_it_builds_with_sub_aggrs(): void
    {
        $aggr = new TermsAggregation(':field:');
        $aggr->add(':sub:', new TermsAggregation(':field2:'));

        self::assertEquals([
            'terms' => [
                'field' => ':field:',
                'size' => 10
            ],
            'aggs' => [
                ':sub:' => [
                    'terms' => [
                        'field' => ':field2:',
                        'size' => 10
                    ]
                ]
            ]
        ], $aggr->build());
    }
}
