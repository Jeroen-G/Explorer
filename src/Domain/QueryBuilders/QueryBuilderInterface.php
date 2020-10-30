<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\QueryBuilders;

use JeroenG\Explorer\Domain\Syntax\SyntaxInterface;

interface QueryBuilderInterface
{
    public function add(string $type, SyntaxInterface $syntax): void;

    public function build(): array;
}
