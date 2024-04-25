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
            if (array_key_exists('doc_count', $rawAggregation)) {
                $aggregations = array_merge($aggregations, $this->parseNestedAggregations($rawAggregation));
                continue;
            }

            $aggregations[] = new AggregationResult($name, $rawAggregation['buckets'] ?? $rawAggregation);
        }

        return $aggregations;
    }

    public function count(): int
    {
        return $this->rawResults['hits']['total']['value'];
    }

    /** @return AggregationResult[] */
    private function parseNestedAggregations(array $rawAggregation): array
    {
        $aggregations = [];
        foreach ($rawAggregation as $nestedAggregationName => $rawNestedAggregation) {
            if (isset($rawNestedAggregation['buckets'])) {
                $aggregations[] = new AggregationResult($nestedAggregationName, $rawNestedAggregation['buckets']);
            }

            if (isset($rawNestedAggregation['doc_count'])) {
                $nested = $this->parseNestedAggregations($rawNestedAggregation);
                foreach ($nested as $item) {
                    $aggregations[] = $item;
                }
            }
        }

        return $aggregations;
    }
}
