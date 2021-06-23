<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Application;

class AggregationResult
{
    private string $name;

    private array $buckets;

    public function __construct(string $name, array $buckets)
    {
        $this->name = $name;
        $this->buckets = $buckets;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function values(): array
    {
        return $this->buckets;
    }
}
