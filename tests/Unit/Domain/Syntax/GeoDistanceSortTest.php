<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Domain\Syntax;

use InvalidArgumentException;
use JeroenG\Explorer\Domain\Syntax\GeoDistanceSort;
use JeroenG\Explorer\Infrastructure\Scout\ScoutSearchCommandBuilder;
use PHPUnit\Framework\TestCase;

final class GeoDistanceSortTest extends TestCase
{
    public function test_it_builds_a_geo_distance_sort(): void
    {
        $subject = new GeoDistanceSort('location', 52.37, 4.89);

        self::assertSame([
            '_geo_distance' => [
                'location' => ['lat' => 52.37, 'lon' => 4.89],
                'order' => 'asc',
            ],
        ], $subject->build());
    }

    public function test_it_builds_with_all_options(): void
    {
        $subject = new GeoDistanceSort(
            'location',
            52.37,
            4.89,
            'desc',
            'km',
            GeoDistanceSort::MODE_MAX,
            GeoDistanceSort::DISTANCE_TYPE_PLANE,
        );

        self::assertSame([
            '_geo_distance' => [
                'location' => ['lat' => 52.37, 'lon' => 4.89],
                'order' => 'desc',
                'unit' => 'km',
                'mode' => 'max',
                'distance_type' => 'plane',
            ],
        ], $subject->build());
    }

    public function test_it_can_be_used_in_the_search_command(): void
    {
        $command = new ScoutSearchCommandBuilder();
        $command->setSort([new GeoDistanceSort('location', 52.37, 4.89, unit: 'km')]);

        self::assertTrue($command->hasSort());
        self::assertSame([
            ['_geo_distance' => ['location' => ['lat' => 52.37, 'lon' => 4.89], 'order' => 'asc', 'unit' => 'km']],
        ], $command->getSort());
    }

    public function test_it_rejects_an_invalid_latitude(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new GeoDistanceSort('location', 91.0, 4.89);
    }

    public function test_it_rejects_an_invalid_longitude(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new GeoDistanceSort('location', 52.37, -181.0);
    }

    public function test_it_rejects_an_invalid_mode(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new GeoDistanceSort('location', 52.37, 4.89, mode: 'sum');
    }

    public function test_it_rejects_an_invalid_distance_type(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new GeoDistanceSort('location', 52.37, 4.89, distanceType: 'flat');
    }

    public function test_it_rejects_an_invalid_order(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new GeoDistanceSort('location', 52.37, 4.89, 'sideways');
    }
}
