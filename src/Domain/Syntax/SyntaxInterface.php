<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

interface SyntaxInterface
{
    public function build(): array;
}
