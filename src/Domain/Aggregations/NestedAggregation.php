<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Aggregations;

final class NestedAggregation implements AggregationSyntaxInterface
{
    private string $field;

    private array $aggregations = [];

    public function __construct(string $field)
    {
        $this->field = $field;
    }

    public function add(string $name, AggregationSyntaxInterface $agg): void
    {
        $this->aggregations[$name] = $agg;
    }

    public function build(): array
    {
        return [
            'nested' => [
                'path' => $this->field,
            ],
            'aggs' => $this->buildNestedAggregations()
        ];
    }

    private function buildNestedAggregations(): array
    {
        $data = [];
        foreach ($this->aggregations as $name => $aggregation) {
            $data[$name] = $aggregation->build();
        }
        return $data;
    }
}
