<?php

namespace JeroenG\Explorer\Domain\Syntax;

class   Term implements SyntaxInterface
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
        return ['term' => [$this->field => $this->value]];
    }
}
