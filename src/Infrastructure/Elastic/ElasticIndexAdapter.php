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

        if($indexConfiguration->getAliasConfiguration()->shouldPruneAliases()) {
            $this->pruneAlias($indexConfiguration->getAliasConfiguration());
        }
    }

    public function delete(IndexConfigurationInterface $indexConfiguration): void
    {
        if(!$indexConfiguration->isAliased()) {
            $this->client->indices()->delete(['index' => $indexConfiguration->getName()]);
            return;
        }

        $aliasConfiguration = $indexConfiguration->getAliasConfiguration();

        $this->client->indices()->deleteAlias([
            'index' => '_all',
            'name' => $aliasConfiguration->getAliasName()
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

    public function deleteAllIndicesWithAliasName(string $aliasName): void
    {
        $indicesForAlias = $this->client->indices()->getAlias(['name' => $aliasName]);

        foreach($indicesForAlias as $index => $data) {
            $this->delete(IndexConfiguration::create($index, [], [], null));
        }
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
                    // In one transaction, move existing alias(es) to history and map alias to new index.
                    'actions' => [
                        ['add' => ['index' => $aliasConfiguration->getAliasName() . '*', 'alias' => $aliasConfiguration->getAliasName().'-history']],
                        ['remove' => ['index' => '*', 'alias' => $aliasConfiguration->getAliasName()]],
                        ['add' => ['index' => $aliasConfiguration->getIndexName(), 'alias' => $aliasConfiguration->getAliasName()]],
                    ],
                ],
            ]);
        }
    }

    private function pruneAlias(IndexAliasConfigurationInterface $indexAliasConfiguration): void
    {
        $exists = $this->client->indices()->existsAlias(['name' => $indexAliasConfiguration->getAliasName() . '-history']);

        if (!$exists) {
            return;
        }

        $indicesForAlias = $this->client->indices()->getAlias(['name' => $indexAliasConfiguration->getAliasName() . '-history']);
        $latestIndex = $indexAliasConfiguration->getIndexName();

        foreach($indicesForAlias as $index => $data) {
            if ($index === $latestIndex) {
                continue;
            }

            if (count($data['aliases']) > 1) {
                continue;
            }

            $this->delete(IndexConfiguration::create($index, [], []));
        }
    }
}
