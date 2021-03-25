<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Query;

use JeroenG\Explorer\Domain\Syntax\Sort;
use JeroenG\Explorer\Domain\Syntax\SyntaxInterface;

class Query implements SyntaxInterface
{
    public static function with(SyntaxInterface $syntax)
    {
        $query = new self();
        $query->query = $syntax;
        return $query;
    }

    private ?int $offset = null;

    private ?int $limit = null;

    private array $fields = [];

    /** @var Sort[] */
    private array $sort = [];

    private SyntaxInterface $query;

    public function build(): array
    {
        $query = [
            'query' => $this->query->build()
        ];
        if ($this->hasPagination()) {
            $query['from'] = $this->offset;
            $query['size'] = $this->limit;
        }

        if ($this->hasSort()) {
            $query['sort'] = $this->buildSort();
        }

        if ($this->hasFields()) {
            $query['fields'] = $this->fields;
        }

        return $query;
    }

    private function hasPagination(): bool
    {
        return $this->offset !== null && $this->limit !== null;
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

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getSort(): array
    {
        return $this->sort;
    }

    public function getQuery(): SyntaxInterface
    {
        return $this->query;
    }
}
