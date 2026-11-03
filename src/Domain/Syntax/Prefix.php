<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

class Prefix implements SyntaxInterface
{
    private string $field;

    private string $value;

    private float $boost;

    private bool $caseInsensitive;

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
        return ['prefix' => [
            $this->field => [
                'value' => $this->value,
                'boost' => $this->boost,
                'case_insensitive' => $this->caseInsensitive,
                'rewrite' => $this->rewrite,
            ],
        ]];
    }
}
