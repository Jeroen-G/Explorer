<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\Scout;

use JeroenG\Explorer\Application\Paginator;
use JeroenG\Explorer\Domain\Aggregations\AggregationSyntaxInterface;
use JeroenG\Explorer\Domain\Query\QueryProperties\QueryProperty;
use JeroenG\Explorer\Domain\Syntax\Compound\BoolQuery;

class Builder extends \Laravel\Scout\Builder
{
    public array $must = [];

    public array $should = [];

    public array $filter = [];

    public array $fields = [];

    public ?BoolQuery $compound = null;

    public array $aggregations = [];

    public array $queryProperties = [];

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

    public function newCompound(?BoolQuery $compound): self
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
