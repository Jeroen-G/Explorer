<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Query\QueryProperties;

class MinScoreFilter implements QueryProperty
{

    private ?float $min_score = null;

    public function __construct(
        ?float $min_score = null
    ) {
        $this->min_score = $min_score;
    }

    public function build(): array
    {
        if (is_null($this->min_score)) {
            return [];
        }

        return [
            'min_score' => $this->min_score,
        ];
    }
}
