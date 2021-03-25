<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax\Compound\ScoreFunction;

use JeroenG\Explorer\Domain\Syntax\SyntaxInterface;

class ScoreFunction
{
    private ?SyntaxInterface $filter = null;

    private ?int $weight;

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
        if($this->filter !== null) {
            $scoreFunction['filter'] = $this->filter->build();
        }
        if($this->weight !== null) {
            $scoreFunction['weight'] = $this->weight;
        }

        return $scoreFunction;
    }
}
