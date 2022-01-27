<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\IndexManagement;

use Illuminate\Contracts\Container\BindingResolutionException;
use JsonSchema\Exception\RuntimeException;
use Webmozart\Assert\Assert;

final class IndexConfiguration implements IndexConfigurationInterface
{
    private string $name;

    private ?string $model;

    private array $settings = [];

    private array $properties = [];

    private ?IndexAliasConfigurationInterface $aliasConfiguration;

    private string $defaultPrefix = '';

    private function __construct(string $name, ?string $model, ?IndexAliasConfigurationInterface $aliasConfiguration = null)
    {
        $this->model = $model;
        $this->name = $name;
        $this->aliasConfiguration = $aliasConfiguration;
    }

    public static function create(
        string                            $name,
        array                             $properties,
        array                             $settings,
        ?string                           $model = null,
        ?IndexAliasConfigurationInterface $aliasConfiguration = null
    ): self
    {
        $config = new self($name, $model, $aliasConfiguration);
        $config->properties = $properties;
        $config->settings = $settings;

        return $config;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNameWithPrefix(): string
    {
        return $this->getScoutPrefix() . $this->name;
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

    public function getWriteIndexName()
    {
        return $this->isAliased() ? $this->getAliasConfiguration()->getWriteAliasName() : $this->getNameWithPrefix();
    }

    private function getAliasedName(): string
    {
        return sprintf('%s-%d', $this->name, time());
    }

    private function getScoutPrefix(): ?string
    {
        try {
            return config('scout.prefix');
        }catch (BindingResolutionException $e){
            return '';
        }
    }
}
