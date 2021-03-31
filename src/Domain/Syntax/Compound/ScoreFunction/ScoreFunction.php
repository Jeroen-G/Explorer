<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax\Compound\ScoreFunction;

use JeroenG\Explorer\Domain\Syntax\SyntaxInterface;

class ScoreFunction
{
    private ?SyntaxInterface $filter = null;

    private ?int $weight = null;

    public function setFilter(?SyntaxInterface $filter): void
    {
        $this->filter = $filter;
    }

    public function setWeight(?int $weight): void
    {
        $this->weight = $weight;
    }

    public function build(): array
    {
        $scoreFunction = [];
        if (!is_null($this->filter)) {
            $scoreFunction['filter'] = $this->filter->build();
        }
        if (!is_null($this->weight)) {
            $scoreFunction['weight'] = $this->weight;
        }

        return $scoreFunction;
    }
}
