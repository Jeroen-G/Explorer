<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

class DisjunctionMax implements SyntaxInterface
{
    /** @var SyntaxInterface[] */
    public array $queries = [];

    /** @param SyntaxInterface[] $queries */
    public static function queries(array $queries): self
    {
        $dismax = new self();
        $dismax->queries = $queries;
        return $dismax;
    }

    public function build(): array
    {
        return [
            'dis_max' => [
                'queries' => array_map(fn ($query) => $query->build(), $this->queries),
            ],
        ];
    }
}
