<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\IndexManagement;

interface IndexConfigurationInterface
{
    public function getName(): string;

    public function getModel(): ?string;

    public function isAliased(): bool;

    public function getAliasConfiguration(): IndexAliasConfigurationInterface;

    public function getProperties(): array;

    public function getSettings(): array;

    public function toArray(): array;

    public function getConfiguredIndexName(): string;
}
