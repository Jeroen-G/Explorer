<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Aggregations;

class NestedAggregation implements AggregationSyntaxInterface
{
    private string $field;

    private array $aggregations = [];

    public function __construct(string $field)
    {
        $this->field = $field;
    }

    public function add(string $name, AggregationSyntaxInterface $agg)
    {
        $this->aggregations[$name] = $agg;
    }

    public function build(): array
    {
        return [
            'nested' => [
                'path' => $this->field,
            ],
            'aggs' => $this->buildNestedAggs()
        ];
    }

    protected function buildNestedAggs(): array
    {
        $data = [];
        foreach ($this->aggregations as $name => $aggregation) {
            $data[$name] = $aggregation->build();
        }
        return $data;
    }
}
