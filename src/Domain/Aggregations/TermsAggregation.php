<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Aggregations;

final class TermsAggregation implements AggregationSyntaxInterface
{
    private string $field;

    public function __construct(string $field)
    {
        $this->field = $field;
    }

    public function build(): array
    {
        return [
            'terms' => [
                'field' => $this->field,
            ]
        ];
    }
}
