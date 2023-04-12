<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\IndexManagement;

use JeroenG\Explorer\Application\Aliased;
use JeroenG\Explorer\Application\Explored;
use JeroenG\Explorer\Application\IndexSettings;
use JeroenG\Explorer\Domain\IndexManagement\IndexConfigurationBuilder;
use JeroenG\Explorer\Domain\IndexManagement\IndexConfigurationInterface;
use JeroenG\Explorer\Domain\IndexManagement\IndexConfigurationNotFoundException;
use JeroenG\Explorer\Domain\IndexManagement\IndexConfigurationRepositoryInterface;
use RuntimeException;

class ElasticIndexConfigurationRepository implements IndexConfigurationRepositoryInterface
{
    private array $indexConfigurations;

    private bool $pruneOldAliases;

    private array $defaultSettings;

    public function __construct(array $indexConfigurations, bool $pruneOldAliases = true, array $defaultSettings = [])
    {
        $this->indexConfigurations = $indexConfigurations;
        $this->pruneOldAliases = $pruneOldAliases;
        $this->defaultSettings = $defaultSettings;
    }

    /**
     * @return iterable<IndexConfigurationInterface>
     */
    public function getConfigurations(): iterable
    {
        foreach ($this->indexConfigurations as $key => $index) {
            if (is_string($index)) {
                yield $this->getIndexConfigurationByClass($index);
            } elseif (is_string($key) && is_array($index)) {
                yield $this->getIndexConfigurationByArray($key, $index);
            } else {
                $data = var_export($index, true);
                throw new RuntimeException(sprintf('Unable to create index for "%s"', $data));
            }
        }
    }

    public function findForIndex(string $index): IndexConfigurationInterface
    {
        foreach ($this->getConfigurations() as $indexConfiguration) {
            if ($indexConfiguration->getName() === $index) {
                return $indexConfiguration;
            }
        }

        throw IndexConfigurationNotFoundException::index($index);
    }

    private function getIndexConfigurationByClass(string $index): IndexConfigurationInterface
    {
        $class = (new $index());

        if (!$class instanceof Explored) {
            throw new RuntimeException(sprintf('Unable to create index %s, ensure it implements Explored', $index));
        }

        $settings = $class instanceof IndexSettings ? $class->indexSettings() : $this->defaultSettings;

        $builder = IndexConfigurationBuilder::forExploredModel($class)
            ->withProperties($class->mappableAs())
            ->withSettings($settings);

        if ($class instanceof Aliased) {
            $builder = $builder->asAliased($this->pruneOldAliases);
        }

        return $builder->buildIndexConfiguration();
    }

    private function getIndexConfigurationByArray(string $name, array $index): IndexConfigurationInterface
    {
        $useAlias = $index['aliased'] ?? false;

        $builder = IndexConfigurationBuilder::named($name)
            ->withProperties($index['properties'] ?? [])
            ->withSettings($index['settings'] ?? $this->defaultSettings)
            ->withModel($index['model'] ?? null);

        if ($useAlias) {
            $builder = $builder->asAliased($this->pruneOldAliases);
        }

        return $builder->buildIndexConfiguration();
    }
}
