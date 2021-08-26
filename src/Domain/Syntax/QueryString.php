<?php

declare(strict_types=1);


namespace JeroenG\Explorer\Domain\Syntax;

class QueryString implements SyntaxInterface
{
    private string $queryString;

    private float $boost;

    public function __construct(string $queryString, float $boost = 1.0)
    {
        $this->queryString = $queryString;
        $this->boost = $boost;
    }

    public function build(): array
    {
        return [
            'query_string' => [
                'query' => $this->queryString,
                'boost' => $this->boost,
            ],
        ];
    }
}
