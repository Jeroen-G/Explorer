<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

use Webmozart\Assert\Assert;

class Terms implements SyntaxInterface
{
    private string $field;

    private array $values;

    private ?float $boost;

    public function __construct(string $field, array $values = [], ?float $boost = 1.0)
    {
        // ES accepts scalars for term queries in practice (string|int|float|bool).
        Assert::notEmpty($values, 'Terms values must not be empty.');
        Assert::allNotNull($values, 'Terms values must not contain null.');
        Assert::allScalar($values, 'Terms values must be scalars (string|int|float|bool).');

        $this->field = $field;
        $this->values = $values;
        $this->boost = $boost;
    }

    public function build(): array
    {
        return ['terms' => [
            $this->field => $this->values,
            'boost' => $this->boost,
        ]];
    }
}
