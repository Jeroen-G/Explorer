<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\Scout;

use JeroenG\Explorer\Application\SearchableFields;
use JeroenG\Explorer\Application\SearchCommandInterface;
use JeroenG\Explorer\Domain\Query\Query;
use JeroenG\Explorer\Domain\Query\QueryProperties\QueryProperty;
use JeroenG\Explorer\Domain\Syntax\Compound\BoolQuery;
use JeroenG\Explorer\Domain\Syntax\Compound\QueryType;
use JeroenG\Explorer\Domain\Syntax\MultiMatch;
use JeroenG\Explorer\Domain\Syntax\Sort;
use JeroenG\Explorer\Domain\Syntax\Term;
use JeroenG\Explorer\Domain\Syntax\Terms;
use Laravel\Scout\Builder;
use Webmozart\Assert\Assert;

class ScoutSearchCommandBuilder implements SearchCommandInterface
{
    private array $must = [];

    private array $should = [];

    private array $filter = [];

    private array $wheres = [];

    private array $whereIns = [];

    private array $fields = [];

    /** @var Sort[]  */
    private array $sort = [];

    private array $aggregations = [];

    private string $query = '';

    private ?string $index = null;

    private ?int $offset = null;

    private ?int $limit = null;

    private ?array $defaultSearchFields = null;

    private array $queryProperties = [];

    private BoolQuery $boolQuery;

    public function __construct()
    {
        $this->boolQuery = new BoolQuery();
    }

    public static function wrap(Builder $builder): ScoutSearchCommandBuilder
    {
        $normalizedBuilder = new self();

        $normalizedBuilder->setMust($builder->must ?? []);
        $normalizedBuilder->setShould($builder->should ?? []);
        $normalizedBuilder->setFilter($builder->filter ?? []);
        $normalizedBuilder->setWheres($builder->wheres);
        $normalizedBuilder->setWhereIns($builder->whereIns);
        $normalizedBuilder->setQuery($builder->query ?: '');
        $normalizedBuilder->setAggregations($builder->aggregations ?? []);
        $normalizedBuilder->setSort(self::getSorts($builder));
        $normalizedBuilder->setFields($builder->fields ?? []);
        $normalizedBuilder->setBoolQuery($builder->compound ?? new BoolQuery());
        $normalizedBuilder->setLimit($builder->limit);
        $normalizedBuilder->queryProperties = $builder->queryProperties ?? [];

        $index = $builder->index ?: $builder->model->searchableAs();

        if ($builder->model instanceof SearchableFields) {
            $normalizedBuilder->setDefaultSearchFields($builder->model->getSearchableFields());
        }

        $normalizedBuilder->setIndex($index);

        if($builder->callback){
            call_user_func($builder->callback, $normalizedBuilder);
        }

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

    public function getWheres(): array
    {
        return $this->wheres;
    }

    public function getWhereIns(): array
    {
        return $this->whereIns;
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

    public function getBoolQuery(): BoolQuery
    {
        return $this->boolQuery ?? new BoolQuery();
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

    public function setWheres(array $wheres): void
    {
        $this->wheres = $wheres;
    }

    public function setWhereIns(array $whereIns): void
    {
        $this->whereIns = $whereIns;
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

    public function setBoolQuery(BoolQuery $boolQuery): void
    {
        $this->boolQuery = $boolQuery;
    }

    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    public function hasFields(): bool
    {
        return !empty($this->fields);
    }

    public function setAggregations(array $aggregations): void
    {
        $this->aggregations = $aggregations;
    }

    public function getAggregations(): array
    {
        return $this->aggregations;
    }

    public function buildQuery(): array
    {
        $query = new Query();
        $query->setFields($this->fields);
        $query->setSort($this->sort);
        $query->setLimit($this->limit);
        $query->setOffset($this->offset);
        $query->addQueryProperties(...$this->queryProperties);

        foreach ($this->getAggregations() as $name => $aggregation) {
            $query->addAggregation($name, $aggregation);
        }

        $compound = $this->boolQuery->clone();

        $compound->addMany(QueryType::MUST, $this->getMust());
        $compound->addMany(QueryType::SHOULD, $this->getShould());
        $compound->addMany(QueryType::FILTER, $this->getFilter());

        if (!empty($this->query)) {
            $compound->add('must', new MultiMatch($this->query, $this->getDefaultSearchFields()));
        }

        foreach ($this->wheres as $field => $value) {
            $compound->add('filter', new Term($field, $value));
        }

        foreach ($this->whereIns as $field => $values) {
            $compound->add('filter', new Terms($field, $values));
        }

        $query->setQuery($compound);

        return $query->build();
    }

    public function addQueryProperties(QueryProperty ...$queryProperties): void
    {
        array_push($this->queryProperties, ...$queryProperties);
    }

    public function getQueryProperties(): array
    {
        return $this->queryProperties;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    /** @return Sort[] */
    private static function getSorts(Builder $builder): array
    {
        return array_map(static function($order) {
            return $order instanceof Sort ? $order : new Sort($order['column'], $order['direction']);
        }, $builder->orders);
    }
}
