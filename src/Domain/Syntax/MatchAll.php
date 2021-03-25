<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

class MatchAll implements SyntaxInterface
{
    public function build(): array
    {
        return ['match_all' => (object)[] ];
    }
}
