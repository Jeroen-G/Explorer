<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Aggregations;

final class TermsAggregation implements AggregationSyntaxInterface
{
    private string $field;

    private int $size;

    public function __construct(string $field, int $size = 10)
    {
        $this->field = $field;
        $this->size = $size;
    }

    public function build(): array
    {
        return [
            'terms' => [
                'field' => $this->field,
                'size' => $this->size,
            ]
        ];
    }
}
