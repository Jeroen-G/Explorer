<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Aggregations;

use JeroenG\Explorer\Domain\Syntax\Sort;

final class TopHitsAggregation implements AggregationSyntaxInterface
{
    private ?int $size;
    private ?Sort $sort;

    public function __construct(int $size = 3, Sort $sort = null)
    {
        $this->size = $size;
        $this->sort = $sort;

    }

    public function build(): array
    {
        $aggs = ['top_hits' => []];
        if ($this->size !== null) {
            $aggs['top_hits']['size'] = $this->size;
        }
        if ($this->sort instanceof Sort) {
            $aggs['top_hits']['sort'] = $this->sort->build();
        }
        if (empty($aggs['top_hits'])) {
            $aggs['top_hits'] = (object)[];
        }
        return $aggs;
    }
}
