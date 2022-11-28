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

    public function __construct(array $indexConfigurations, $pruneOldAliases = true)
    {
        $this->indexConfigurations = $indexConfigurations;
        $this->pruneOldAliases = $pruneOldAliases;
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

        $builder = IndexConfigurationBuilder::forExploredModel($class)
            ->withProperties($class->mappableAs());

        if ($class instanceof IndexSettings) {
            $builder = $builder->withSettings($class->indexSettings());
        }

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
            ->withSettings($index['settings'] ?? [])
            ->withModel($index['model'] ?? null);

        if ($useAlias) {
            $builder = $builder->asAliased($this->pruneOldAliases);
        }

        return $builder->buildIndexConfiguration();
    }
}
