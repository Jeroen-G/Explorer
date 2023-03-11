<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\Scout;

use JeroenG\Explorer\Application\Paginator;
use JeroenG\Explorer\Domain\Aggregations\AggregationSyntaxInterface;
use JeroenG\Explorer\Domain\Query\QueryProperties\QueryProperty;

class Builder extends \Laravel\Scout\Builder
{
    public array $must;

    public array $should;

    public array $filter;

    public array $fields;

    public array $compound;

    public array $aggregations;

    public array $queryProperties;

    public function must($must): self
    {
        $this->must[] = $must;
        return $this;
    }

    public function should($should): self
    {
        $this->should[] = $should;
        return $this;
    }

    public function filter($filter): self
    {
        $this->filter[] = $filter;
        return $this;
    }

    public function field(string $field): self
    {
        $this->fields[] = $field;
        return $this;
    }

    public function newCompound($compound): self
    {
        $this->compound = $compound;
        return $this;
    }

    public function aggregation(string $name, AggregationSyntaxInterface $aggregation): self
    {
        $this->aggregations[$name] = $aggregation;
        return $this;
    }

    public function property(QueryProperty $queryProperty): self
    {
        $this->queryProperties[] = $queryProperty;
        return $this;
    }
}
