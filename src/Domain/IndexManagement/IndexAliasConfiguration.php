<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\IndexManagement;

final class IndexAliasConfiguration implements IndexAliasConfigurationInterface
{
    private string $name;

    private string $suffix;

    private bool $pruneOldAliases;

    private function __construct(string $name, string $suffix, bool $pruneOldAliases)
    {
        $this->name = $name;
        $this->suffix = $suffix;
        $this->pruneOldAliases = $pruneOldAliases;
    }

    public static function create(string $name, ?string $suffix = null, bool $pruneOldAliases = true): IndexAliasConfiguration
    {
        $suffix = $suffix ?? (string) time();
        return new self($name, $suffix, $pruneOldAliases);
    }

    public function shouldOldAliasesBePruned(): bool
    {
        return $this->pruneOldAliases;
    }

    public function getIndexName(): string
    {
        return $this->name . '-' . $this->suffix;
    }

    public function getAliasName(): string
    {
        return $this->name;
    }
}
