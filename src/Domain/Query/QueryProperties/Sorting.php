<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Query\QueryProperties;

use JeroenG\Explorer\Domain\Syntax\Sort;

final class Sorting implements QueryProperty, Combinable
{
    /** @param Sort[] $sorts */
    private function __construct(private array $sorts = [])
    {
    }

    public static function for(Sort ...$sort): self
    {
        return new self($sort);
    }

    public function combine(...$self): QueryProperty
    {
        $all = $this->sorts;

        /** @var Sorting[] $self */
        foreach($self as $sorting) {
            array_push($all, ...$sorting->sorts);
        }

        return new self($all);
    }

    public function build(): array
    {
        return [
            'sort' => array_map(
                static fn (Sort $sort) => $sort->build(),
                $this->sorts,
            ),
        ];
    }
}