<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Domain\Query\QueryProperties;

use JeroenG\Explorer\Domain\Query\QueryProperties\SourceFilter;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

final class SourceFilterTest extends TestCase
{
    /** @dataProvider provideSourceFilterValues */
    public function test_it_builds(array $expectedValue, SourceFilter $subject): void
    {
        Assert::assertSame([ '_source' => $expectedValue ], $subject->build());
    }

    public function test_it_builds_empty_array(): void
    {
        Assert::assertSame([], SourceFilter::empty()->build());
    }

    public static function provideSourceFilterValues(): iterable
    {
        yield 'include 2 fields' => [
            [ 'include' => ['a', 'b'] ],
            SourceFilter::empty()->include('a', 'b'),
        ];

        yield 'exclude 2 fields' => [
            [ 'exclude' => ['a', 'b'] ],
            SourceFilter::empty()->exclude('a', 'b'),
        ];

        yield 'exclude 2 fields, includes 2 fields' => [
            [ 'include' => ['a', 'b'], 'exclude' => ['c', 'd'] ],
            SourceFilter::empty()->include('a', 'b')->exclude('c', 'd'),
        ];
    }
}
