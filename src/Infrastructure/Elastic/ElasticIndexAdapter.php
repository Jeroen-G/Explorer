<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\Elastic;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Response\Elasticsearch;
use JeroenG\Explorer\Application\IndexAdapterInterface;
use JeroenG\Explorer\Domain\IndexManagement\AliasedIndexConfiguration;
use JeroenG\Explorer\Domain\IndexManagement\IndexAliasConfigurationInterface;
use JeroenG\Explorer\Domain\IndexManagement\DirectIndexConfiguration;
use JeroenG\Explorer\Domain\IndexManagement\IndexConfigurationInterface;

final class ElasticIndexAdapter implements IndexAdapterInterface
{
    public function __construct(private Client $client)
    {
    }

    public function create(IndexConfigurationInterface $indexConfiguration): void
    {
        if (!$indexConfiguration instanceof AliasedIndexConfiguration) {
            $this->createIndex(
                $indexConfiguration->getName(),
                $indexConfiguration->getProperties(),
                $indexConfiguration->getSettings(),
            );

            return;
        }

        $this->createNewWriteIndex($indexConfiguration);
        $this->pointToAlias($indexConfiguration);
    }

    public function pointToAlias(IndexConfigurationInterface $indexConfiguration): void
    {
        if (!$indexConfiguration instanceof AliasedIndexConfiguration) {
            return;
        }

        $this->makeAliasActive($indexConfiguration->getAliasConfiguration());

        if ($indexConfiguration->getAliasConfiguration()->shouldOldAliasesBePruned()) {
            $this->pruneAliases($indexConfiguration->getAliasConfiguration());
        }
    }

    public function delete(IndexConfigurationInterface $indexConfiguration): void
    {
        if (!$indexConfiguration instanceof AliasedIndexConfiguration) {
            $this->client->indices()->delete(['index' => $indexConfiguration->getName()]);
            return;
        }

        $aliasConfiguration = $indexConfiguration->getAliasConfiguration();
        $aliasName = $aliasConfiguration->getHistoryAliasName();

        if (!$this->client->indices()->existsAlias(['name' => $aliasName])->asBool()) {
            return;
        }

        $indicesForAlias = $this->client->indices()->getAlias(['name' => $aliasName])->asArray();

        foreach ($indicesForAlias as $index => $data) {
            $this->client->indices()->delete(['index' => $index]);
        }
    }

    public function flush(string $index): void
    {
        $matchAllQuery = ['query' => ['match_all' => (object)[]]];
        $this->client->deleteByQuery([
            'index' => $index,
            'body' => $matchAllQuery
        ]);
    }

    public function getWriteIndexName(IndexAliasConfigurationInterface $aliasConfiguration): ?string
    {
        /** @var ElasticSearch $aliasConfig */
        $aliasConfig = $this->client->indices()->getAlias(['name' => $aliasConfiguration->getWriteAliasName()]);

        return last(array_keys($aliasConfig->asArray()));
    }

    public function createNewWriteIndex(AliasedIndexConfiguration $indexConfiguration): string
    {
        $aliasConfig = $indexConfiguration->getAliasConfiguration();
        $indexName = $this->getUniqueAliasIndexName($aliasConfig);

        $this->createIndex($indexName, $indexConfiguration->getProperties(), $indexConfiguration->getSettings());

        $this->client->indices()->updateAliases([
            'body' => [
                'actions' => [
                    ['add' => ['index' => $aliasConfig->getAliasName() . '*', 'alias' => $aliasConfig->getHistoryAliasName()]],
                    ['remove' => ['index' => $aliasConfig->getAliasName() . '*', 'alias' => $aliasConfig->getWriteAliasName()]],
                    ['add' => ['index' => $indexName, 'alias' => $aliasConfig->getWriteAliasName()]],
                ],
            ],
        ]);

        return $indexName;
    }

    public function ensureIndex(IndexConfigurationInterface $indexConfiguration): void
    {
        $exists = $this->client->indices()->exists([
            'index' => $indexConfiguration->getWriteIndexName(),
        ])->asBool();

        if (!$exists) {
            $this->create($indexConfiguration);
        }
    }

    private function makeAliasActive(IndexAliasConfigurationInterface $aliasConfiguration): void
    {
        $exists = $this->client->indices()->existsAlias(['name' => $aliasConfiguration->getAliasName()])->asBool();
        $index = $this->getWriteIndexName($aliasConfiguration);
        $alias = $aliasConfiguration->getAliasName();

        if (!$exists) {
            $this->client->indices()->putAlias([
                'index' => $index,
                'name' => $aliasConfiguration->getAliasName(),
            ]);
        } else {
            $this->client->indices()->updateAliases([
                'body' => [
                    'actions' => [
                        ['add' => ['index' => $alias . '*', 'alias' => $aliasConfiguration->getHistoryAliasName()]],
                        ['remove' => ['index' => $alias . '*', 'alias' => $aliasConfiguration->getAliasName()]],
                        ['add' => ['index' => $index, 'alias' => $aliasConfiguration->getAliasName()]],
                    ],
                ],
            ]);
        }
    }

    private function pruneAliases(IndexAliasConfigurationInterface $indexAliasConfiguration): void
    {
        $aliasName = $indexAliasConfiguration->getHistoryAliasName();
        if (!$this->client->indices()->existsAlias(['name' => $aliasName])->asBool()) {
            return;
        }

        $indicesForAlias = $this->client->indices()->getAlias(['name' => $aliasName])->asArray();
        $writeAlias = $this->getWriteIndexName($indexAliasConfiguration);

        foreach ($indicesForAlias as $index => $data) {
            if ($index === $writeAlias) {
                continue;
            }

            if (count($data['aliases']) > 1) {
                continue;
            }

            $this->delete(DirectIndexConfiguration::create(
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

        while ($this->client->indices()->exists(['index' => $name])->asBool()) {
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
