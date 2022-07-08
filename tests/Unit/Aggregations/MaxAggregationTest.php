<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Aggregations;

use JeroenG\Explorer\Domain\Aggregations\MaxAggregation;
use PHPUnit\Framework\TestCase;

class MaxAggregationTest extends TestCase
{
    public function test_it_builds(): void
    {
        $aggregation = new MaxAggregation(':field:');
        self::assertEquals([
            'max' => [
                'field' => ':field:',
            ]
        ], $aggregation->build());
    }
}
