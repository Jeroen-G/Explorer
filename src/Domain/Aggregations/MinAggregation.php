<?php

namespace JeroenG\Explorer\Domain\Aggregations;

class MinAggregation implements AggregationSyntaxInterface
{
    private string $field;

    public function __construct(string $field)
    {
        $this->field = $field;
    }

    public function build(): array
    {
        return [
            'min' => [
                'field' => $this->field
            ]
        ];
    }
}
