<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

use Webmozart\Assert\Assert;

/**
 * Sorts documents by their distance to an origin, using a `geo_point` field.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/sort-search-results.html#geo-sorting
 */
class GeoDistanceSort extends Sort
{
    public const MODE_MIN = 'min';

    public const MODE_MAX = 'max';

    public const MODE_MEDIAN = 'median';

    public const MODE_AVG = 'avg';

    public const DISTANCE_TYPE_ARC = 'arc';

    public const DISTANCE_TYPE_PLANE = 'plane';

    private string $field;

    private float $latitude;

    private float $longitude;

    private string $order;

    private ?string $unit;

    private ?string $mode;

    private ?string $distanceType;

    public function __construct(
        string $field,
        float $latitude,
        float $longitude,
        string $order = SortOrder::ASCENDING,
        ?string $unit = null,
        ?string $mode = null,
        ?string $distanceType = null,
    ) {
        Assert::range($latitude, -90, 90);
        Assert::range($longitude, -180, 180);
        Assert::nullOrNotEmpty($unit);
        Assert::nullOrInArray($mode, [self::MODE_MIN, self::MODE_MAX, self::MODE_MEDIAN, self::MODE_AVG]);
        Assert::nullOrInArray($distanceType, [self::DISTANCE_TYPE_ARC, self::DISTANCE_TYPE_PLANE]);

        // Validates the order; `missing` is not supported by _geo_distance sorts, hence plain strings only.
        parent::__construct($field, SortOrder::fromString($order));

        $this->field = $field;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->order = $order;
        $this->unit = $unit;
        $this->mode = $mode;
        $this->distanceType = $distanceType;
    }

    public function build(): array
    {
        $options = ['order' => $this->order];

        if ($this->unit !== null) {
            $options['unit'] = $this->unit;
        }

        if ($this->mode !== null) {
            $options['mode'] = $this->mode;
        }

        if ($this->distanceType !== null) {
            $options['distance_type'] = $this->distanceType;
        }

        return [
            '_geo_distance' => [
                $this->field => ['lat' => $this->latitude, 'lon' => $this->longitude],
                ...$options,
            ],
        ];
    }
}
