<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\IndexManagement;

final class DirectIndexConfiguration implements IndexConfigurationInterface
{
    private function __construct(
        private string $name,
        private array $properties,
        private array $settings,
        private ?string $model = null,
    )
    {
    }

    public static function create(
        string $name,
        array $properties,
        array $settings,
        ?string $model = null,
    ): self {
        return new self(
            name: $name,
            properties: $properties,
            settings: $settings,
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

    public function getReadIndexName(): string
    {
        return $this->name;
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
        return $this->name;
    }
}
