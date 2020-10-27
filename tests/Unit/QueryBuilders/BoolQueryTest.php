<?php

namespace JeroenG\Explorer\Tests\Unit\QueryBuilders;

use JeroenG\Explorer\Domain\QueryBuilders\BoolQuery;
use PHPUnit\Framework\TestCase;

class BoolQueryTest extends TestCase
{
    public function test_it_can_build_an_empty_query(): void
    {
        $subject = new BoolQuery();

        $expected = [
            'bool' => [
                'must' => [],
                'should' => [],
                'filter' => [],
            ],
        ];

        $query = $subject->build();

        self::assertSame($expected, $query);
    }
}
