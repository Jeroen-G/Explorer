<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Application;

use Countable;

class Results implements Countable
{
    private array $rawResults;

    public function __construct(array $rawResults)
    {
        $this->rawResults = $rawResults;
    }

    public function hits(): array
    {
        return $this->rawResults['hits']['hits'];
    }

    /** @return AggregationResult[] */
    public function aggregations(): array
    {
        if (!isset($this->rawResults['aggregations'])) {
            return [];
        }

        $aggregations = [];

        foreach ($this->rawResults['aggregations'] as $name => $rawAggregation) {
            $aggregations[] = new AggregationResult($name, $rawAggregation['buckets']);
        }

        return $aggregations;
    }

    public function count(): int
    {
        return $this->rawResults['hits']['total']['value'];
    }
}
