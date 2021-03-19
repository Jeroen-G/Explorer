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

    public static function empty(string $name): self
    {
        return new self($name);
    }

    public static function create(string $name, array $properties, array $settings): self
    {
        $config = new self($name);
        $config->properties = $properties;
        $config->settings = $settings;

        return $config;
    }

    public function getName(): string
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

    public function toArray(): array
    {
        $config = [
            'index' => $this->getName(),
        ];

        if (!empty($this->settings)) {
            $config['body']['settings'] = $this->getSettings();
        }

        if (!empty($this->properties)) {
            $config['body']['mappings'] = [
                'properties' => $this->getProperties()
            ];
        }

        return $config;
    }
}
