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

    private array $fields = [];
  
    /** @var Sort[]  */
    private array $sort = [];

    private string $query = '';

    private ?string $index = null;

    private ?int $offset = null;

    private ?int $limit = null;

    private ?array $defaultSearchFields = null;

    public static function wrap(Builder $builder): BuildCommand
    {
        $normalizedBuilder = new self();

        $normalizedBuilder->setMust($builder->must ?? []);
        $normalizedBuilder->setShould($builder->should ?? []);
        $normalizedBuilder->setFilter($builder->filter ?? []);
        $normalizedBuilder->setWhere($builder->where ?? []);
        $normalizedBuilder->setQuery($builder->query ?? '');
        $normalizedBuilder->setSort(self::getSorts($builder));
        $normalizedBuilder->setFields($builder->fields ?? []);
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
        return !empty($this->sort);
    }

    public function getSort(): array
    {
        if ($this->hasSort()) {
            return array_map(static fn ($item) => $item->build(), $this->sort);
        }

        return [];
    }

    public function getFields(): array
    {
        return $this->fields;
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

    public function setSort(array $sort): void
    {
        Assert::allIsInstanceOf($sort, Sort::class);
        $this->sort = $sort;
    }

    public function setCompound(CompoundSyntaxInterface $compound): void
    {
        $this->compound = $compound;
    }

    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    public function hasFields(): bool
    {
        return !empty($this->fields);
    }
  
    /** @return Sort[] */
    private static function getSorts(Builder $builder): array
    {
        return array_map(static fn ($order) => new Sort($order['column'], $order['direction']), $builder->orders);
    }
}
