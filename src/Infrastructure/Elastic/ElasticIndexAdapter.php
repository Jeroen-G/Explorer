<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\Elastic;

use Elasticsearch\Client;
use JeroenG\Explorer\Application\IndexAdapterInterface;
use JeroenG\Explorer\Domain\IndexManagement\IndexAliasConfigurationInterface;
use JeroenG\Explorer\Domain\IndexManagement\IndexConfiguration;
use JeroenG\Explorer\Domain\IndexManagement\IndexConfigurationInterface;

final class ElasticIndexAdapter implements IndexAdapterInterface
{
    private Client $client;

    public function __construct(ElasticClientFactory $clientFactory)
    {
        $this->client = $clientFactory->client();
    }

    public function create(IndexConfigurationInterface $indexConfiguration, string $indexName = null): void
    {
        $indexName = $indexName ?? $indexConfiguration->getReadIndexName();

        $this->client->indices()->create([
            'index' => $indexName,
            'body' => [
                'settings' => $indexConfiguration->getSettings(),
                'mappings' => [
                    'properties' => $indexConfiguration->getProperties(),
                ],
            ],
        ]);
    }

    public function pointToAlias(IndexConfigurationInterface $indexConfiguration): void
    {
        if (!$indexConfiguration->isAliased()) {
            return;
        }

        $this->makeAliasActive($indexConfiguration->getAliasConfiguration());

        if ($indexConfiguration->getAliasConfiguration()->shouldOldAliasesBePruned()) {
            $this->pruneAliases($indexConfiguration->getAliasConfiguration());
        }
    }

    public function delete(IndexConfigurationInterface $indexConfiguration): void
    {
        $aliasConfiguration = $indexConfiguration->getAliasConfiguration();

        if (!$indexConfiguration->isAliased()) {
            $this->client->indices()->delete(['index' => $indexConfiguration->getName()]);
            return;
        }

        $exists = $this->client->indices()->existsAlias(['name' => $aliasConfiguration->getAliasName()]);

        if (!$exists) {
            $this->client->indices()->putAlias([
                'index' => $aliasConfiguration->getIndexName(),
                'name' => $aliasConfiguration->getAliasName(),
            ]);
        }

        $this->pruneAliases($indexConfiguration->getAliasConfiguration());

        $this->client->indices()->deleteAlias([
            'index' => '_all',
            'alias' => $aliasConfiguration->getAliasName()
        ]);
    }

    public function flush(string $index): void
    {
        $matchAllQuery = [ 'query' => [ 'match_all' => (object)[] ] ];
        $this->client->deleteByQuery([
            'index' => $index,
            'body' => $matchAllQuery
        ]);
    }

    public function getRemoteConfiguration(IndexConfigurationInterface $indexConfiguration): ?IndexConfiguration
    {
        $indexName = $this->getIndexName($indexConfiguration);
        if (is_null($indexName)) {
            return null;
        }

        $getParams = ['name' => $indexName];
        $settings = $this->client->indices()->getSettings($getParams);
        $mapping = $this->client->indices()->getMapping($getParams);
        $aliasedConfig = $indexConfiguration->isAliased() ? $indexConfiguration->getAliasConfiguration() : null;

        return IndexConfiguration::create(
            $indexConfiguration->getName(),
            $mapping['properties'] ?? [],
            $settings,
            $indexConfiguration->getModel(),
            $aliasedConfig
        );
    }

    public function getInactiveIndexName(IndexAliasConfigurationInterface $aliasConfiguration): ?string
    {
        $aliasConfig = $this->client->indices()->getAlias(['name' => $aliasConfiguration->getWriteAliasName() ]);

        return $aliasConfig['index'] ?? null;
    }

    public function getInactiveIndexForAlias(IndexConfigurationInterface $indexConfiguration): ?string
    {
        if (!$indexConfiguration->isAliased()) {
            return null;
        }

        $aliasConfig = $indexConfiguration->getAliasConfiguration();
        $exists = $this->client->indices()->existsAlias([ 'name' => $aliasConfig->getWriteAliasName() ]);

        if (!$exists) {
            return $this->createNewInactiveIndex($indexConfiguration);
        }

        $aliasData = $this->client->indices()->existsAlias(['name' => $aliasConfig->getWriteAliasName() ]);
        return $aliasData['index'] ?? null;
    }

    public function createNewInactiveIndex(IndexConfigurationInterface $indexConfiguration): string
    {
        $aliasConfig = $indexConfiguration->getAliasConfiguration();
        $indexName = $this->getUniqueAliasIndexName($aliasConfig);

        $this->create($indexConfiguration, $indexName);

        $this->client->indices()->putAlias([
            'index' => $indexName,
            'name' => $aliasConfig->getWriteAliasName(),
        ]);

        return $indexName;
    }

    private function makeAliasActive(IndexAliasConfigurationInterface $aliasConfiguration): void
    {
        $exists = $this->client->indices()->existsAlias(['name' => $aliasConfiguration->getAliasName()]);
        $index = $this->getInactiveIndexName($aliasConfiguration);

        if (!$exists) {
            $this->client->indices()->putAlias([
                'index' => $index,
                'name' => $aliasConfiguration->getAliasName(),
            ]);
        } else {
            $this->client->indices()->updateAliases([
                'body' => [
                    'actions' => [
                        ['add' => ['index' => $aliasConfiguration->getAliasName() . '*', 'alias' => $aliasConfiguration->getAliasName() . '-history']],
                        ['remove' => ['index' => '*', 'alias' => $aliasConfiguration->getAliasName()]],
                        ['add' => ['index' => $index, 'alias' => $aliasConfiguration->getAliasName()]],
                    ],
                ],
            ]);
        }
    }

    private function pruneAliases(IndexAliasConfigurationInterface $indexAliasConfiguration): void
    {
        $indicesForAlias = $this->client->indices()->getAlias(['name' => $indexAliasConfiguration->getAliasName() . '-history']);
        $latestIndex = $indexAliasConfiguration->getIndexName();

        foreach ($indicesForAlias as $index => $data) {
            if ($index === $latestIndex) {
                continue;
            }

            if (count($data['aliases']) > 1) {
                continue;
            }

            $this->delete(IndexConfiguration::create($index, [], []));
        }
    }

    private function getIndexName(IndexConfigurationInterface $indexConfiguration): ?string
    {
        if (!$indexConfiguration->isAliased()) {
            return $indexConfiguration->getName();
        }

        $aliasName = $indexConfiguration->getAliasConfiguration()->getAliasName();
        if (!$this->client->indices()->existsAlias([ 'name' => $aliasName ])) {
            return null;
        }

        $alias = $this->client->indices()->getAlias([ 'name' => $aliasName ]);
        if (!isset($alias[0])) {
            return null;
        }

        return $alias[0]['aliases'][0] ?? null;
    }

    private function getUniqueAliasIndexName(IndexAliasConfigurationInterface $aliasConfig): string
    {
        $name = $aliasConfig->getIndexName() . '_' . time();
        $iX = 0;

        while ($this->client->indices()->exists([ 'index' => $name ])) {
            $name .= '_' . $iX++;
        }

        return $name;
    }
}
