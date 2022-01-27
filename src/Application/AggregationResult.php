<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Application;

class AggregationResult
{
    private string $name;

    private array $buckets;

    private array $aggs;

    private ?string $value;

    public function __construct(string $name, array $buckets = [], array $aggs = [], string $value = null)
    {
        $this->name = $name;
        $this->buckets = $buckets;
        $this->aggs = $aggs;
        $this->value = $value;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function values(): array
    {
        return $this->buckets;
    }

    public function value(): ?string
    {
        return $this->value;
    }

    public function aggregations(): array
    {
        return $this->aggs;
    }
}
