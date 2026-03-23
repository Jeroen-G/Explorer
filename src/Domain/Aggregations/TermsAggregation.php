<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Aggregations;

final class TermsAggregation implements AggregationSyntaxInterface
{
    private string $field;

    private int $size;

    private array $aggregations;

    public function __construct(string $field, int $size = 10)
    {
        $this->field = $field;
        $this->size = $size;
        $this->aggregations = [];
    }

    public function add(string $name, TermsAggregation $aggregation): void
    {
        $this->aggregations[$name] = $aggregation;
    }

    public function build(): array
    {
        $build = [
            'terms' => [
                'field' => $this->field,
                'size' => $this->size,
            ],
        ];

        if (count($this->aggregations) > 0) {
            $build['aggs'] = $this->buildSubAggregations();
        }

        return $build;
    }

    private function buildSubAggregations(): array
    {
        $subAggregations = [];

        foreach ($this->aggregations as $name => $aggregation) {
            $subAggregations[$name] = $aggregation->build();
        }

        return $subAggregations;
    }
}
