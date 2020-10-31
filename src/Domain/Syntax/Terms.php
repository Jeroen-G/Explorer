<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

use Webmozart\Assert\Assert;

class Terms implements SyntaxInterface
{
    private string $field;

    private array $values;

    public function __construct(string $field, array $values = [])
    {
        $this->field = $field;
        $this->values = $values;
        Assert::allStringNotEmpty($values);
    }

    public function build(): array
    {
        return ['terms' => [$this->field => $this->values]];
    }
}
