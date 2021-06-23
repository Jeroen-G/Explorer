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

    public function aggregations(): array
    {
        return $this->rawResults['aggregations'];
    }

    public function count(): int
    {
        return $this->rawResults['hits']['total']['value'];
    }
}
