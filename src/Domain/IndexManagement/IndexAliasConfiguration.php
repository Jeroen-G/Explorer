<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\IndexManagement;

final class IndexAliasConfiguration implements IndexAliasConfigurationInterface
{
    private string $name;

    private bool $pruneOldAliases;

    private function __construct(string $name, bool $pruneOldAliases)
    {
        $this->name = $name;
        $this->pruneOldAliases = $pruneOldAliases;
    }

    public static function create(string $name, bool $pruneOldAliases): IndexAliasConfiguration
    {
        return new self($name, $pruneOldAliases);
    }

    public function shouldOldAliasesBePruned(): bool
    {
        return $this->pruneOldAliases;
    }

    public function getIndexName(): string
    {
        return $this->name;
    }

    public function getAliasName(): string
    {
        return $this->name;
    }

    public function getHistoryAliasName(): string
    {
        return $this->name . '-history';
    }

    public function getWriteAliasName(): string
    {
        return $this->name . '-write';
    }
}
