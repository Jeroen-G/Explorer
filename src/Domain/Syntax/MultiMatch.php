<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

class MultiMatch implements SyntaxInterface
{
    /** @var mixed */
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function build(): array
    {
        return ['multi_match' => ['query' => $this->value]];
    }
}
