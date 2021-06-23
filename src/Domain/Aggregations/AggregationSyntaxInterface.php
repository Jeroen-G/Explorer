<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Aggregations;

interface AggregationSyntaxInterface
{
    public function build(): array;
}
