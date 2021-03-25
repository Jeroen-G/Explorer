<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Application;

use JeroenG\Explorer\Domain\Query\Query;
use Webmozart\Assert\Assert;

class SearchCommand implements SearchCommandInterface
{
    private ?string $index;

    private ?Query $query;

    public function __construct(?string $index = null, ?Query $query = null)
    {
        $this->index = $index;
        $this->query = $query;
    }

    public function getIndex(): string
    {
        Assert::notNull($this->index);
        return $this->index;
    }

    public function setIndex(string $index): void
    {
        $this->index = $index;
    }

    public function setQuery(?Query $query): void
    {
        $this->query = $query;
    }

    public function buildQuery(): array
    {
        return $this->query->build();
    }
}
