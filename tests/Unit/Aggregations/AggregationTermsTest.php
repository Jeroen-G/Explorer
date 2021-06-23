<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Aggregations;

use JeroenG\Explorer\Domain\Aggregations\TermsAggregation;
use PHPUnit\Framework\TestCase;

class AggregationTermsTest extends TestCase
{
    public function test_it_builds(): void
    {
        $aggr = new TermsAggregation(':field:');
        self::assertEquals([
            'terms' => [
                'field' => ':field:'
            ]
        ], $aggr->build());
    }
}
