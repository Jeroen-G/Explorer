<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Application;

use JeroenG\Explorer\Domain\Compound\BoolQuery;
use JeroenG\Explorer\Domain\Compound\CompoundSyntaxInterface;
use JeroenG\Explorer\Domain\Syntax\Sort;
use Laravel\Scout\Builder;
use Webmozart\Assert\Assert;

class BuildCommand
{
    private CompoundSyntaxInterface $compound;

    private array $must = [];

    private array $should = [];

    private array $filter = [];

    private array $where = [];

    private string $query = '';

    private ?string $index = null;

    private ?int $offset = null;

    private ?int $limit = null;

    private ?Sort $sort = null;

    private ?array $defaultSearchFields = null;

    public static function wrap(Builder $builder): BuildCommand
    {
        $normalizedBuilder = new self();

        $normalizedBuilder->setMust($builder->must ?? []);
        $normalizedBuilder->setShould($builder->should ?? []);
        $normalizedBuilder->setFilter($builder->filter ?? []);
        $normalizedBuilder->setWhere($builder->where ?? []);
        $normalizedBuilder->setQuery($builder->query ?? '');
        $normalizedBuilder->setSort($builder->sort ?? null);
        $normalizedBuilder->setCompound($builder->compound ?? new BoolQuery());

        $index = $builder->index ?: $builder->model->searchableAs();

        if ($builder->model instanceof SearchableFields) {
            $normalizedBuilder->setDefaultSearchFields($builder->model->getSearchableFields());
        }

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

    public function getDefaultSearchFields(): ?array
    {
        return $this->defaultSearchFields;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function hasSort(): bool
    {
        return !is_null($this->sort);
    }

    public function getSort(): array
    {
        if ($this->hasSort()) {
            return $this->sort->build();
        }

        return [];
    }

    public function getCompound(): CompoundSyntaxInterface
    {
        return $this->compound ?? new BoolQuery();
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

    public function setDefaultSearchFields(?array $fields): void
    {
        $this->defaultSearchFields = $fields;
    }

    public function setOffset(?int $offset): void
    {
        $this->offset = $offset;
    }

    public function setLimit(?int $limit): void
    {
        $this->limit = $limit;
    }

    public function setSort(?Sort $sort = null): void
    {
        $this->sort = $sort;
    }

    public function setCompound(CompoundSyntaxInterface $compound): void
    {
        $this->compound = $compound;
    }
}
