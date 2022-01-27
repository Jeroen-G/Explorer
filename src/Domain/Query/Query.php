<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Query;

use JeroenG\Explorer\Domain\Aggregations\AggregationSyntaxInterface;
use JeroenG\Explorer\Domain\Syntax\Sort;
use JeroenG\Explorer\Domain\Syntax\SyntaxInterface;

class Query implements SyntaxInterface
{
    private ?int $offset = null;

    private ?int $limit = null;

    /** @var Rescoring[]  */
    private array $rescoring = [];

    private array $fields = [];

    /** @var Sort[] */
    private array $sort = [];

    private array $highlight = [];

    private ?string $collapse = null;

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

        if ($this->hasSort()) {
            $query['sort'] = $this->buildSort();
        }

        if ($this->hasFields()) {
            $query['_source'] = $this->fields;
        }

        if ($this->hasRescoring()) {
            $query['rescore'] = $this->buildRescoring();
        }

        if ($this->hasAggregations()) {
            $query['aggs'] = array_map(
                fn (AggregationSyntaxInterface $value) => $value->build(),
                $this->aggregations
            );
        }
        if ($this->hasHighlight()) {
            $query['highlight'] = [
                'fields' => $this->highlight
            ];
        }

        if ($this->hasCollapse()) {
            $query['collapse'] = [
                'field' => $this->collapse
            ];
        }

        return $query;
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
        $this->sort = $sort;
    }

    public function setQuery(SyntaxInterface $query): void
    {
        $this->query = $query;
    }

    public function addRescoring(Rescoring $rescoring): void
    {
        $this->rescoring[] = $rescoring;
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

    private function hasSort(): bool
    {
        return !empty($this->sort);
    }

    private function hasFields(): bool
    {
        return !empty($this->fields);
    }

    private function buildSort(): array
    {
        return array_map(static fn ($item) => $item->build(), $this->sort);
    }

    private function hasRescoring(): bool
    {
        return !empty($this->rescoring);
    }

    private function buildRescoring(): array
    {
        return array_map(fn (Rescoring $rescore) => $rescore->build(), $this->rescoring);
    }


    public function setHighlight(array $highlight): void
    {
        $this->highlight = $highlight;
    }

    public function setCollapse(?string $collapse): void
    {
        $this->collapse = $collapse;
    }

    private function hasHighlight(): bool
    {
        return !empty($this->highlight);
    }

    public function hasCollapse(): bool
    {
        return null !== $this->collapse;
    }
}
