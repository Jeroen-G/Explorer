<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Aggregations;

use JeroenG\Explorer\Domain\Syntax\SyntaxInterface;

final class FilterAggregation implements AggregationSyntaxInterface
{
    private SyntaxInterface $syntax;

    /** @var AggregationSyntaxInterface [] */
    private array $aggs;

    public function __construct(SyntaxInterface $syntax, array $aggs = [])
    {
        $this->syntax = $syntax;
        $this->aggs = $aggs;
    }

    public function build(): array
    {
        $aggs = ['filter' => $this->syntax->build()];
        foreach ($this->aggs as $key => $value) {
            $aggs['aggs'][$key] = $value->build();
        }
        return $aggs;
    }
}
