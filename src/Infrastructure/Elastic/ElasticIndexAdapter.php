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

    public function create(IndexConfigurationInterface $indexConfiguration): void
    {
        if ($indexConfiguration->isAliased()) {
            $this->createNewWriteIndex($indexConfiguration);
            $this->pointToAlias($indexConfiguration);
            return;
        }

        $this->createIndex(
            $indexConfiguration->getName(),
            $indexConfiguration->getProperties(),
            $indexConfiguration->getSettings(),
        );
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
        if (!$indexConfiguration->isAliased()) {
            $this->client->indices()->delete(['index' => $indexConfiguration->getName()]);
            return;
        }

        $aliasConfiguration = $indexConfiguration->getAliasConfiguration();
        $aliasName = $aliasConfiguration->getHistoryAliasName();

        if (!$this->client->indices()->existsAlias(['name' => $aliasName])) {
            return;
        }

        $indicesForAlias = $this->client->indices()->getAlias(['name' => $aliasName]);

        foreach ($indicesForAlias as $index => $data) {
            $this->client->indices()->delete(['index' => $index]);
        }
    }

    public function flush(string $index): void
    {
        $matchAllQuery = [ 'query' => [ 'match_all' => (object)[] ] ];
        $this->client->deleteByQuery([
            'index' => $index,
            'body' => $matchAllQuery
        ]);
    }

    public function getWriteIndexName(IndexAliasConfigurationInterface $aliasConfiguration): ?string
    {
        $aliasConfig = $this->client->indices()->getAlias(['name' => $aliasConfiguration->getWriteAliasName() ]);

        return last(array_keys($aliasConfig));
    }

    public function createNewWriteIndex(IndexConfigurationInterface $indexConfiguration): string
    {
        $aliasConfig = $indexConfiguration->getAliasConfiguration();
        $indexName = $this->getUniqueAliasIndexName($aliasConfig);

        $this->createIndex($indexName, $indexConfiguration->getProperties(), $indexConfiguration->getSettings());

        $this->client->indices()->updateAliases([
            'body' => [
                'actions' => [
                    ['remove' => ['index' => '*', 'alias' => $aliasConfig->getWriteAliasName()]],
                    ['add' => ['index' => $indexName, 'alias' => $aliasConfig->getWriteAliasName()]],
                ],
            ],
        ]);

        return $indexName;
    }

    private function makeAliasActive(IndexAliasConfigurationInterface $aliasConfiguration): void
    {
        $exists = $this->client->indices()->existsAlias(['name' => $aliasConfiguration->getAliasName()]);
        $index = $this->getWriteIndexName($aliasConfiguration);

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
        $aliasName = $indexAliasConfiguration->getHistoryAliasName();
        if (!$this->client->indices()->existsAlias(['name' => $aliasName])) {
            return;
        }

        $indicesForAlias = $this->client->indices()->getAlias(['name' => $aliasName]);
        $writeAlias = $this->getWriteIndexName($indexAliasConfiguration);

        foreach ($indicesForAlias as $index => $data) {
            if ($index === $writeAlias) {
                continue;
            }

            if (count($data['aliases']) > 1) {
                continue;
            }

            $this->delete(IndexConfiguration::create(
                name: $index,
                properties: [],
                settings: [],
            ));
        }
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

    private function createIndex(string $index, array $properties, array $settings = []): void
    {
        $body = [];

        if (!empty($settings)) {
            $body['settings'] = $settings;
        }

        $body['mappings'] = [
            'properties' => $properties,
        ];

        $this->client->indices()->create([
            'index' => $index,
            'body' => $body,
        ]);
    }
}
