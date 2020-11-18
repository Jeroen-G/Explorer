<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Compound;

use JeroenG\Explorer\Domain\Syntax\SyntaxInterface;

interface CompoundSyntaxInterface
{
    public function add(string $type, SyntaxInterface $syntax): void;

    /**
     * @param string $type
     * @param SyntaxInterface[] $syntax
     */
    public function addMany(string $type, array $syntax): void;

    public function build(): array;

    public function must(SyntaxInterface $syntax): void;

    public function should(SyntaxInterface $syntax): void;

    public function filter(SyntaxInterface $syntax): void;
}
