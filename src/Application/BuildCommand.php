<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Application;

use Laravel\Scout\Builder;
use Webmozart\Assert\Assert;

class BuildCommand
{
    private array $must = [];
    private array $should = [];
    private array $filter = [];
    private array $where = [];
    private string $query = '';
    private ?string $index = null;
    private ?int $offset = null;
    private ?int $limit = null;

    public static function wrap(Builder $builder): BuildCommand
    {
        $normalizedBuilder = new self();

        $normalizedBuilder->setMust($builder->must ?? []);
        $normalizedBuilder->setShould($builder->should ?? []);
        $normalizedBuilder->setFilter($builder->filter ?? []);
        $normalizedBuilder->setWhere($builder->where ?? []);
        $normalizedBuilder->setQuery($builder->query ?? '');

        $index = $builder->index ?: $builder->model->searchableAs();

        $normalizedBuilder->setIndex($index);

        return $normalizedBuilder;
    }

    public function getMust(): array
    {
        return $this->must;
    }

    public function getShould(): array
    {
        return $this->should;
    }

    public function getFilter(): array
    {
        return $this->filter;
    }

    public function getWhere(): array
    {
        return $this->where;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getIndex(): string
    {
        Assert::notNull($this->index);
        return $this->index;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setMust(array $must): void
    {
        $this->must = $must;
    }

    public function setShould(array $should): void
    {
        $this->should = $should;
    }

    public function setFilter(array $filter): void
    {
        $this->filter = $filter;
    }

    public function setWhere(array $where): void
    {
        $this->where = $where;
    }

    public function setQuery(string $query): void
    {
        $this->query = $query;
    }

    public function setIndex(string $index): void
    {
        $this->index = $index;
    }

    public function setOffset(?int $offset): void
    {
        $this->offset = $offset;
    }

    public function setLimit(?int $limit): void
    {
        $this->limit = $limit;
    }
}
