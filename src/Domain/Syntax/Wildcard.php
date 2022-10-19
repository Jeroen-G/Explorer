<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

class Wildcard implements SyntaxInterface
{
    private string $field;

    private string $value;

    private float $boost;

    private bool $caseInsensitive = false;

    private string $rewrite;

    public function __construct(
        string $field,
        string $value,
        float $boost = 1.0,
        bool $caseInsensitive = false,
        string $rewrite = 'constant_score',
    ) {
        $this->field = $field;
        $this->value = $value;
        $this->boost = $boost;
        $this->caseInsensitive = $caseInsensitive;
        $this->rewrite = $rewrite;
    }

    public function build(): array
    {
        $query = [
            'value' => $this->value,
            'boost' => $this->boost,
            'case_insensitive' => $this->caseInsensitive,
            'rewrite' => $this->rewrite,
        ];

        return ['wildcard' => [ $this->field => $query ] ];
    }
}
