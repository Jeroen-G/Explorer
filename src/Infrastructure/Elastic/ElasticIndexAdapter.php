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
        $this->client->indices()->create($indexConfiguration->toArray());
    }

    public function pointToAlias(IndexConfigurationInterface $indexConfiguration): void
    {
        if (!$indexConfiguration->isAliased()) {
            return;
        }

        $this->upsertAlias($indexConfiguration->getAliasConfiguration());

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

    public function getActualConfiguration(IndexConfigurationInterface $indexConfiguration): ?IndexConfiguration
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
            $aliasedConfig
        );
    }

    private function upsertAlias(IndexAliasConfigurationInterface $aliasConfiguration): void
    {
        $exists = $this->client->indices()->existsAlias(['name' => $aliasConfiguration->getAliasName()]);

        if (!$exists) {
            $this->client->indices()->putAlias([
                'index' => $aliasConfiguration->getIndexName(),
                'name' => $aliasConfiguration->getAliasName(),
            ]);
        } else {
            $this->client->indices()->updateAliases([
                'body' => [
                    'actions' => [
                        ['add' => ['index' => $aliasConfiguration->getAliasName() . '*', 'alias' => $aliasConfiguration->getAliasName() . '-history']],
                        ['remove' => ['index' => '*', 'alias' => $aliasConfiguration->getAliasName()]],
                        ['add' => ['index' => $aliasConfiguration->getIndexName(), 'alias' => $aliasConfiguration->getAliasName()]],
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
        $alias = $this->client->indices()->getAlias([ 'name' => $aliasName ]);
        if (!isset($alias[0])) {
            return null;
        }

        return $alias[0]['aliases'][0] ?? null;
    }
}
