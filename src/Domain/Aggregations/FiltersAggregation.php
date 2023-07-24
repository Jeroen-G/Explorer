<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Aggregations;

use JeroenG\Explorer\Domain\Syntax\SyntaxInterface;

final class FiltersAggregation implements AggregationSyntaxInterface
{
    /** @var AggregationSyntaxInterface [] */
    private array $aggs;

    /** @var SyntaxInterface [] */
    private array $filters;

    public function __construct(
        array           $filters,
        array           $aggs = []
    )
    {
        $this->aggs = $aggs;
        $this->filters = $filters;
    }

    public function build(): array
    {
        $aggs = ['filters' => ['filters' => []]];
        foreach ($this->filters as $value) {
            $aggs['filters']['filters'][] = $value->build();
        }
        foreach ($this->aggs as $key => $value) {
            $aggs['aggs'][$key] = $value->build();
        }
        return $aggs;
    }
}
