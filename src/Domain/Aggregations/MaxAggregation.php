<?php

namespace JeroenG\Explorer\Domain\Aggregations;

class MaxAggregation implements AggregationSyntaxInterface
{
    private string $field;

    public function __construct(string $field)
    {
        $this->field = $field;
    }

    public function build(): array
    {
        return [
            'max' => [
                'field' => $this->field
            ]
        ];
    }
}
