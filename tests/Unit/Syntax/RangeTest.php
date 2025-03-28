<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Syntax;

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
                    'boost' => 1.0,
                ],
            ]
        ];

        $query = $subject->build();

        self::assertSame($expected, $query);
    }

    public function test_it_accepts_a_different_boost(): void
    {
        $subject = new Range('age', ['gte' => 18], 5.0);

        $expected = [
            'range' => [
                'age' => [
                    'gte' => 18,
                    'boost' => 5.0,
                ],
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

    public function test_it_accepts_negative_values(): void
    {
        $subject = new Range('rating', ['gte' => -5, 'lte' => 5]);

        $expected = [
            'range' => [
                'rating' => [
                    'gte' => -5,
                    'lte' => 5,
                    'boost' => 1.0,
                ],
            ]
        ];

        $query = $subject->build();

        self::assertSame($expected, $query);
    }

    public function test_it_accepts_float_values(): void
    {
        $subject = new Range('rating', ['gte' => 0.5, 'lte' => 5.33]);

        $expected = [
            'range' => [
                'rating' => [
                    'gte' => 0.5,
                    'lte' => 5.33,
                    'boost' => 1.0,
                ],
            ]
        ];

        $query = $subject->build();

        self::assertSame($expected, $query);
    }

    public function test_it_accepts_string_values(): void
    {
        $subject = new Range('date', ['gte' => '2020-01-01T00:00:00', 'lte' => '2021-01-01T00:00:00']);

        $expected = [
            'range' => [
                'date' => [
                    'gte' => '2020-01-01T00:00:00',
                    'lte' => '2021-01-01T00:00:00',
                    'boost' => 1.0,
                ],
            ]
        ];

        $query = $subject->build();

        self::assertSame($expected, $query);
    }

    public function test_it_accepts_only_one_value(): void
    {
        $subject = new Range('rating', ['gte' => -50.4]);

        $expected = [
            'range' => [
                'rating' => [
                    'gte' => -50.4,
                    'boost' => 1.0
                ],
            ]
        ];

        $query = $subject->build();

        self::assertSame($expected, $query);
    }

    public function test_it_stops_on_null_value(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected a value other than null.');
        new Range('rating', ['gte' => 3.4, 'lte' => null]);
    }

    public function test_it_stops_on_non_numeric_or_string_value(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected one of: "string", "integer", "double". Got: array');
        new Range('rating', ['gte' => 3.4, 'lte' => ['2.0']]);
    }
}
