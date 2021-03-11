<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\IndexManagement;

final class IndexConfiguration implements IndexConfigurationInterface
{
    private string $name;

    private array $settings = [];

    private array $properties = [];

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function create(string $name, array $properties, array $settings): self
    {
        $config = new self($name);
        $config->properties = $properties;
        $config->settings = $settings;
        return $config;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function properties(): array
    {
        return $this->properties;
    }

    public function settings(): array
    {
        return $this->settings;
    }
}
