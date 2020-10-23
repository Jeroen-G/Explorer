<?php

namespace JeroenG\Explorer;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\Collection;
use Laravel\Scout\Builder;
use Laravel\Scout\Engines\Engine;
use Webmozart\Assert\Assert;

class ElasticEngine extends Engine
{
    private Client $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()->build();
    }

    /** @inheritDoc */
    public function update($models): void
    {
        if ($models->isEmpty()) {
            return;
        }

        $models->each(function($model) {
            $data = [
                'index' => $model instanceof Explored ? $model->mappableAs() : $model->searchableAs(),
                'id' => $model->getScoutKey(),
                'body' => $model->toSearchableArray(),
            ];

            $this->client->index($data);
        });
    }

    /** @inheritDoc */
    public function delete($models): void
    {
        if ($models->isEmpty()) {
            return;
        }

        $models->each(function(Explored $model) {
            $data = [
                'index' => $model->mappableAs(),
                'id' => $model->getScoutKey(),
            ];

            $this->client->delete($data);
        });
    }

    /** @inheritDoc */
    public function search(Builder $builder)
    {
        return $this->executeSearch($builder);
    }

    private function executeSearch(Builder $builder)
    {
        return $this->client->search([
            'index' => $builder->index ?: $builder->model->searchableAs(),
            'body'  => [
                'query' => [
                    'multi_match' => [
                        'query' => $builder->query,
                    ]
                ]
            ]
        ]);
    }

    /** @inheritDoc */
    public function paginate(Builder $builder, $perPage, $page)
    {
        // TODO[explorer]: implement pagination
        $this->executeSearch($builder);
    }

    /** @inheritDoc */
    public function mapIds($results): Collection
    {
        return collect($results['hits']['hits'])->pluck('_id')->values();
    }

    /** @inheritDoc */
    public function map(Builder $builder, $results, $model): Collection
    {
        if (count($results['hits']['hits']) === 0) {
            return $model->newCollection();
        }

        $objectIds = $this->mapIds($results)->all();
        $objectIdPositions = array_flip($objectIds);

        return $model->getScoutModelsByIds(
            $builder, $objectIds
        )->filter(function ($model) use ($objectIds) {
            return in_array($model->getScoutKey(), $objectIds, true);
        })->sortBy(function ($model) use ($objectIdPositions) {
            return $objectIdPositions[$model->getScoutKey()];
        })->values();
    }

    /** @inheritDoc */
    public function getTotalCount($results): int
    {
        return $results['total']['value'];
    }

    /** @inheritDoc */
    public function flush($model): void
    {
        $this->client->indices()->flush(['index' => $model->mappableAs()]);
    }
}
