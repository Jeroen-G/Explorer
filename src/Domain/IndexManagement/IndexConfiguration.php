<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\IndexManagement;

class IndexConfiguration
{
    private string $name;

    private array $settings = [];

    private array $properties = [];

    public static function empty(string $name): self
    {
        $config = new self();
        $config->name = $name;
        return $config;
    }

    public static function create(string $name, array $properties, array $settings): self
    {
        $config = new self();
        $config->name = $name;
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
