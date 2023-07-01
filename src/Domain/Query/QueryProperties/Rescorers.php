<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Query\QueryProperties;

final class Rescorers implements QueryProperty, Combinable
{
    private function __construct(
        private array $rescoringQueries = [],
    ) {}

    public static function for(Rescoring ...$rescoring): self
    {
        return new self($rescoring);
    }

    public function build(): array
    {
        return [
            'rescore' => array_map(static fn (Rescoring $rescoring) => $rescoring->build(), $this->rescoringQueries),
        ];
    }

    public function combine(...$self): self
    {
        $all = $this->rescoringQueries;
        foreach($self as $rescorer) {
            array_push($all, ...$rescorer->rescoringQueries);
        }

        return new self($all);
    }
}