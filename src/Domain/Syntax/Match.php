<?php

namespace JeroenG\Explorer\Domain\Syntax;

class Match implements SyntaxInterface
{
    private string $field;

    /** @var mixed */
    private $value;

    public function __construct(string $field, $value)
    {
        $this->field = $field;
        $this->value = $value;
    }

    public function build(): array
    {
        return ['match' => [$this->field => $this->value]];
    }
}
