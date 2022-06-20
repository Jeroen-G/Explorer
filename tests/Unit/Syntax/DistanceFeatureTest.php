<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Syntax;

use JeroenG\Explorer\Domain\Syntax\DistanceFeature;
use PHPUnit\Framework\TestCase;

class DistanceFeatureTest extends TestCase
{
    public function test_it_builds_distance_feature(): void
    {
        $subject = new DistanceFeature(
            'location',
            '3000m',
            [
                'lat' => 'value1',
                'lon' => 'value2',
            ]
        );

        $expected = [
            'distance_feature' => [
                'field' => 'location',
                'pivot' => '3000m',
                'origin' => [
                    'lat' => 'value1',
                    'lon' => 'value2',
                ],
                'boost' => 1.0,
            ]
        ];

        $query = $subject->build();

        self::assertSame($expected, $query);
    }
}
