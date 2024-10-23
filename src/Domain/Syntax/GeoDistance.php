<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

use JeroenG\Explorer\Domain\Syntax\SyntaxInterface;


class GeoDistance implements SyntaxInterface
{
    public const DISTANCE_TYPE_ARC = 'arc';

    public const DISTANCE_TYPE_PLANE = 'palne';

    public const DEFAULT_FIELD = 'location';

    private int $distance;

    private mixed $lat;

    private mixed $lng;

    private ?string $distance_type;
    
    private string $field;

    public function __construct(
        $distance,
        $lat,
        $lng,
        $distance_type = self::DISTANCE_TYPE_ARC,
        $field = self::DEFAULT_FIELD,
    ) {
        $this->distance = $distance;
        $this->lat = $lat;
        $this->lng = $lng;
        $this->field = $field;
        $this->distance_type = $distance_type;
    }

    public function build(): array
    {
        return [
            'geo_distance' => [
                'distance' => (string) $this->distance,
                'distance_type' => (string) $this->distance_type,
                $this->field => [
                    'lat' => (string) $this->lat,
                    'lon' => (string) $this->lng
                ]
            ]
        ];
    }

}
