<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Query;

use JeroenG\Explorer\Domain\Aggregations\AggregationSyntaxInterface;
use JeroenG\Explorer\Domain\Query\QueryProperties\Combinable;
use JeroenG\Explorer\Domain\Query\QueryProperties\QueryProperty;
use JeroenG\Explorer\Domain\Query\QueryProperties\Rescorers;
use JeroenG\Explorer\Domain\Query\QueryProperties\Rescoring;
use JeroenG\Explorer\Domain\Query\QueryProperties\Sorting;
use JeroenG\Explorer\Domain\Syntax\Sort;
use JeroenG\Explorer\Domain\Syntax\SyntaxInterface;
use Webmozart\Assert\Assert;

class Query implements SyntaxInterface
{
    private ?int $offset = null;

    private ?int $limit = null;

    private array $fields = [];

    /** @var QueryProperty[]  */
    private array $queryProperties = [];

    private SyntaxInterface $query;

    /** @var AggregationSyntaxInterface[] */
    private array $aggregations = [];

    public static function with(SyntaxInterface $syntax): Query
    {
        $query = new self();
        $query->query = $syntax;
        return $query;
    }

    public function build(): array
    {
        $query = [
            'query' => $this->query->build()
        ];

        if ($this->hasPagination()) {
            $query['from'] = $this->offset;
        }

        if ($this->hasSize()) {
            $query['size'] = $this->limit;
        }

        if ($this->hasFields()) {
            $query['fields'] = $this->fields;
        }

        if ($this->hasAggregations()) {
            $query['aggs'] = array_map(
                fn (AggregationSyntaxInterface $value) => $value->build(),
                $this->aggregations
            );
        }

        $queryProperties = $this->buildQueryProperties();

        return array_merge($query, ...$queryProperties);
    }

    public function setOffset(?int $offset): void
    {
        $this->offset = $offset;
    }

    public function setLimit(?int $limit): void
    {
        $this->limit = $limit;
    }

    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    public function setSort(array $sort): void
    {
        $this->queryProperties = array_filter(
            $this->queryProperties,
            static fn (QueryProperty $queryProperty) => !($queryProperty instanceof Sorting)
        );

        if (count($sort) > 0) {
            $this->queryProperties[] = Sorting::for(...$sort);
        }
    }

    public function setQuery(SyntaxInterface $query): void
    {
        $this->query = $query;
    }

    public function addQueryProperties(QueryProperty ...$properties): void
    {
        array_push($this->queryProperties, ...$properties);
    }

    public function addRescoring(Rescoring $rescoring): void
    {
        $this->queryProperties[] = Rescorers::for($rescoring);
    }

    public function addAggregation(string $name, AggregationSyntaxInterface $aggregationItem): void
    {
        $this->aggregations[$name] = $aggregationItem;
    }

    public function hasAggregations(): bool
    {
        return !empty($this->aggregations);
    }

    private function hasPagination(): bool
    {
        return !is_null($this->offset);
    }

    private function hasSize(): bool
    {
        return !is_null($this->limit);
    }

    private function hasFields(): bool
    {
        return !empty($this->fields);
    }

    private function buildQueryProperties(): array
    {
        /** @var array<class-string, array<Combinable&QueryProperty>> $allCombinables */
        $allCombinables = [];
        $allQueryProperties = [];

        foreach ($this->queryProperties as $queryProperty) {
            if ($queryProperty instanceof Combinable) {
                $allCombinables[get_class($queryProperty)] = $allCombinables[get_class($queryProperty)] ?? [];
                $allCombinables[get_class($queryProperty)][] = $queryProperty;
            } else {
                $allQueryProperties[] = $queryProperty;
            }
        }

        /** @var array<Combinable&QueryProperty> $sameCombinables */
        foreach ($allCombinables as $sameCombinables) {
            $allQueryProperties[] = $sameCombinables[0]->combine(...array_slice($sameCombinables, 1));
        }

        return array_map(static fn (QueryProperty $queryProperty) => $queryProperty->build(), $allQueryProperties);
    }
}
