<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Application;

class AggregationResult
{
    public function __construct(private string $name, private array $values)
    {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function values(): array
    {
        return $this->values;
    }
}
