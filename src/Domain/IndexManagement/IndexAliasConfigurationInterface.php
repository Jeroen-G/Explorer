<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\IndexManagement;

interface IndexAliasConfigurationInterface
{
    public function shouldPruneAliases(): bool;

    public function getIndexName(): string;

    public function getAliasName(): string;
}
