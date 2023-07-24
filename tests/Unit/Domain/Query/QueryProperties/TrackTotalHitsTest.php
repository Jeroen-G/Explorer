<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Domain\Query\QueryProperties;

use JeroenG\Explorer\Domain\Query\QueryProperties\TrackTotalHits;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

final class TrackTotalHitsTest extends TestCase
{
    /** @dataProvider provideTrackTotalHitCounts */
    public function test_it_tracks_count(int $count): void
    {
        Assert::assertSame([ 'track_total_hits' => $count ], TrackTotalHits::count($count)->build());
    }

    public function test_it_tracks_all(): void
    {
        Assert::assertSame([ 'track_total_hits' => true ], TrackTotalHits::all()->build());
    }

    public function test_it_tracks_none(): void
    {
        Assert::assertSame([ 'track_total_hits' => false ], TrackTotalHits::none()->build());
    }

    public function provideTrackTotalHitCounts(): iterable
    {
        yield 'count 100' => [100];
        yield 'count -100' => [-100];
        yield 'count 0' => [0];
    }
}
