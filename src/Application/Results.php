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

    public function aggregations($root = null): array
    {
        if (!isset($this->rawResults['aggregations'])) {
            return [];
        }
        if (null === $root) {
            $root = $this->rawResults['aggregations'];
        }

        $aggregations = [];
        foreach ($root as $name => $rawAggregation) {
            if (false === is_array($rawAggregation) || $name === 'buckets') {
                continue;
            }
            if (isset($rawAggregation['buckets'])) {
                $buckets = $rawAggregation['buckets'];
                $value = null;
            } elseif (isset($rawAggregation['value'])) {
                $buckets = [];
                $value = (string) $rawAggregation['value'];
            } else {
                $buckets = [];
                $value = null;
            }

            $innerAggregations = $this->aggregations($rawAggregation);
            $aggregations[] = new AggregationResult($name, $buckets, $innerAggregations, $value);
        }

        return $aggregations;
    }

    public function count(): int
    {
        return $this->rawResults['hits']['total']['value'];
    }
}
