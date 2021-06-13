<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

class Wildcard implements SyntaxInterface
{
    private string $field;

    private string $value;

    private float $boost;

    public function __construct (
        string $field,
        string $value,
        float $boost = 1.0
    ) {
        $this->field = $field;
        $this->value = $value;
        $this->boost = $boost;
    }

    public function build(): array
    {
        return [
            'wildcard' => [
                $this->field => [
                    'value' => $this->value,
                    'boost' => $this->boost,
                ]
            ]
        ];
    }
}
