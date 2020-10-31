<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

class Term implements SyntaxInterface
{
    private string $field;

    /** @var mixed */
    private $value;

    private float $boost;

    public function __construct(string $field, $value = null, float $boost = 1.0)
    {
        $this->field = $field;
        $this->value = $value;
        $this->boost = $boost;
    }

    public function build(): array
    {
        return ['term' => [
            $this->field => $this->value,
            'boost' => $this->boost,
        ]];
    }
}
