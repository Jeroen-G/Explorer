<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\IndexManagement;

use Webmozart\Assert\Assert;

final class IndexConfiguration implements IndexConfigurationInterface
{
    private string $name;

    private string $scoutPrefix;

    private ?string $model;

    private array $settings = [];

    private array $properties = [];

    private ?IndexAliasConfigurationInterface $aliasConfiguration;

    private function __construct(string $name, ?string $model, ?IndexAliasConfigurationInterface $aliasConfiguration = null, ?string $scoutPrefix = '')
    {
        $this->model = $model;
        $this->name = $name;
        $this->aliasConfiguration = $aliasConfiguration;
        $this->scoutPrefix = $scoutPrefix;
    }

    public static function create(
        string $name,
        array $properties,
        array $settings,
        ?string $model = null,
        ?IndexAliasConfigurationInterface $aliasConfiguration = null,
        ?string $scoutPrefix = '',
    ): self {
        $config = new self($name, $model, $aliasConfiguration);
        $config->properties = $properties;
        $config->settings = $settings;
        $config->scoutPrefix = $scoutPrefix;

        return $config;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function isAliased(): bool
    {
        return !is_null($this->aliasConfiguration);
    }

    public function getAliasConfiguration(): IndexAliasConfigurationInterface
    {
        Assert::notNull($this->aliasConfiguration);

        return $this->aliasConfiguration;
    }

    public function getReadIndexName(): string
    {
        return $this->isAliased() ? $this->getAliasConfiguration()->getIndexName() : $this->getName();
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
        return $this->isAliased() ? $this->getAliasConfiguration()->getWriteAliasName() : $this->getPrefixedName();
    }

    public function getPrefixedName(): string
    {
        return $this->getScoutPrefix() . $this->getName();
    }

    private function getScoutPrefix(): string
    {
        return $this->scoutPrefix;
    }
}
