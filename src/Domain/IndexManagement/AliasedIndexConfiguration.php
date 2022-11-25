<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\IndexManagement;

final class AliasedIndexConfiguration implements  IndexConfigurationInterface
{
    private function __construct(
        private string $name,
        private IndexAliasConfigurationInterface $aliasConfiguration,
        private array $settings = [],
        private array $properties = [],
        private ?string $model = null,
    ) {
    }

    public static function create(
        string $name,
        IndexAliasConfigurationInterface $aliasConfiguration,
        array $properties,
        array $settings,
        ?string $model = null,
    ): self {
        return new self(
            name: $name,
            aliasConfiguration: $aliasConfiguration,
            settings: $settings,
            properties: $properties,
            model: $model,
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getAliasConfiguration(): IndexAliasConfigurationInterface
    {
        return $this->aliasConfiguration;
    }

    public function getReadIndexName(): string
    {
        return $this->getAliasConfiguration()->getIndexName();
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function getWriteIndexName(): string
    {
        return $this->getAliasConfiguration()->getWriteAliasName();
    }
}