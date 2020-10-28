<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

class Matching implements SyntaxInterface
{
    private string $field;

    /** @var mixed */
    private $value;

    public function __construct(string $field, $value = null)
    {
        $this->field = $field;
        $this->value = $value;
    }

    public function build(): array
    {
        return ['match' => [$this->field => $this->value]];
    }
}
