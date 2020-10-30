<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\QueryBuilders;

use InvalidArgumentException;
use JeroenG\Explorer\Domain\Syntax\Range;
use PHPUnit\Framework\TestCase;

class RangeTest extends TestCase
{
    public function test_it_build_a_range_query(): void
    {
        $subject = new Range('age', ['gte' => 18]);

        $expected = [
            'range' => [
                'age' => [
                    'gte' => 18,
                ]
            ]
        ];

        $query = $subject->build();

        self::assertSame($expected, $query);
    }

    public function test_it_stops_on_invalid_relation_type(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected one of: "gt", "gte", "lt", "lte". Got: "test"');
        new Range('age', ['test' => 2]);
    }

    public function test_it_stops_when_definitions_are_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected a non-empty value. Got: null');
        new Range('age', ['gte' => null]);
    }
}
