<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\IndexManagement;

use JeroenG\Explorer\Application\Explored;

final class IndexConfigurationBuilder
{
    /** @var class-string|null  */
    private ?string $model = null;

    private array $settings = [];

    private array $properties = [];

    private ?IndexAliasConfiguration $aliasedIndexConfiguration = null;

    private IndexMappingNormalizer $indexMappingNormalizer;

    private function __construct(
        private string $name,
    ) {
        $this->indexMappingNormalizer = new IndexMappingNormalizer();
    }

    public static function forExploredModel(Explored $model): self
    {
        return self::named($model->searchableAs())
            ->withModel(get_class($model));
    }

    public static function named(string $name): self
    {
        return new self($name);
    }

    /**
     * @param null|class-string $model
     */
    public function withModel(?string $model): self
    {
        $self = clone $this;

        $self->model = $model;

        return $self;
    }

    public function withSettings(array $settings): self
    {
        $self = clone $this;

        $self->settings = $settings;

        return $self;
    }

    public function withProperties(array $properties): self
    {
        $self = clone $this;

        $self->properties = $this->indexMappingNormalizer->normalize($properties);

        return $self;
    }

    public function asAliased(bool $pruneIndices): self
    {
        $self = clone $this;

        $self->aliasedIndexConfiguration = IndexAliasConfiguration::create($this->name, $pruneIndices);

        return $self;
    }

    public function buildIndexConfiguration(): IndexConfigurationInterface
    {
        if (!is_null($this->aliasedIndexConfiguration)) {
            return AliasedIndexConfiguration::create(
                name: $this->name,
                aliasConfiguration: $this->aliasedIndexConfiguration,
                properties: $this->properties,
                settings: $this->settings,
                model: $this->model,
            );
        }

        return DirectIndexConfiguration::create(
            name: $this->name,
            properties: $this->properties,
            settings: $this->settings,
            model: $this->model,
        );
    }
}