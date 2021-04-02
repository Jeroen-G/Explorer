<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\Scout;

final class Debugger
{
    private array $query;

    public function __construct(array $query)
    {
        $this->query = $query;
    }

    public function array(): array
    {
        return $this->query;
    }

    public function json(): string
    {
        return json_encode($this->query, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
    }
}
