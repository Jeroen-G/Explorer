<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;
use Webmozart\Assert\Assert;

class Term implements SyntaxInterface
{
    private string $field;
    
    private mixed $value;

    private ?float $boost;

    public function __construct(string $field, $value = null, ?float $boost = 1.0)
    {
        // ES accepts scalars for term queries in practice (string|int|float|bool).
        Assert::notNull($value, 'Term value must not be null.');
        Assert::scalar($value, 'Term value must be a scalar (string|int|float|bool).');

        $this->field = $field;
        $this->value = $value;
        $this->boost = $boost;
    }

    public function build(): array
    {
        return ['term' => [
            $this->field => [
                'value' => $this->value,
                'boost' => $this->boost,
            ],
        ]];
    }
}
