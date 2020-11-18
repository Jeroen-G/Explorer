<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\QueryBuilders;

use JeroenG\Explorer\Domain\Syntax\SyntaxInterface;

interface QueryBuilderInterface
{
    public function must(SyntaxInterface $syntax): void;

    public function should(SyntaxInterface $syntax): void;

    public function filter(SyntaxInterface $syntax): void;
}
