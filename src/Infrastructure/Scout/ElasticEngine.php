<?php

namespace JeroenG\Explorer\Infrastructure\Scout;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use JeroenG\Explorer\Application\Explored;
use JeroenG\Explorer\Domain\QueryBuilders\BoolQuery;
use JeroenG\Explorer\Domain\QueryBuilders\QueryType;
use JeroenG\Explorer\Domain\Syntax\MultiMatch;
use JeroenG\Explorer\Domain\Syntax\Term;
use Laravel\Scout\Builder;
use Laravel\Scout\Engines\Engine;
use Webmozart\Assert\Assert;

class ElasticEngine extends Engine
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Update the given model in the index.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $models
     * @return void
     */
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

    /**
     * Remove the given model from the index.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $models
     * @return void
     */
    public function delete($models): void
    {
        if ($models->isEmpty()) {
            return;
        }

        $models->each(function($model) {
            $data = [
                'index' => $model->mappableAs(),
                'id' => $model->getScoutKey(),
            ];

            $this->client->delete($data);
        });
    }

    /**
     * Perform the given search on the engine.
     *
     * @param Builder $builder
     * @return mixed
     */
    public function search(Builder $builder)
    {
        return $this->executeSearch($builder);
    }

    private function executeSearch(Builder $builder)
    {
        $aggregate = new BoolQuery();

        $aggregate->addMany(QueryType::MUST, $builder->must ?? []);
        $aggregate->addMany(QueryType::SHOULD, $builder->should ?? []);
        $aggregate->addMany(QueryType::FILTER, $builder->filter ?? []);

        $aggregate->add('must', new MultiMatch($builder->query));

        foreach($builder->wheres as $field => $value) {
            $aggregate->add('must', new Term($field, $value));
        }

        $index = $builder->index ?:
            ($builder->model instanceof Explored
            ? $builder->model->mappableAs()
            : $builder->model->searchableAs()
        );

        return $this->client->search([
            'index' => $index,
            'body'  => [
                'query' => $aggregate->build(),
            ],
        ]);
    }

    /**
     * Perform the given search on the engine.
     *
     * @param Builder $builder
     * @param  int  $perPage
     * @param  int  $page
     * @return mixed
     */
    public function paginate(Builder $builder, $perPage, $page)
    {
        // TODO[explorer]: implement pagination
        $this->executeSearch($builder);
    }

    /**
     * Pluck and return the primary keys of the given results.
     *
     * @param  mixed  $results
     * @return Collection
     */
    public function mapIds($results): Collection
    {
        return collect($results['hits']['hits'])->pluck('_id')->values();
    }

    /**
     * Map the given results to instances of the given model.
     *
     * @param Builder $builder
     * @param  mixed  $results
     * @param  Model  $model
     * @return \Illuminate\Database\Eloquent\Collection
     */
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
            return in_array($model->getScoutKey(), $objectIds, false);
        })->sortBy(function ($model) use ($objectIdPositions) {
            return $objectIdPositions[$model->getScoutKey()];
        })->values();
    }

    /**
     * Get the total count from a raw result returned by the engine.
     *
     * @param  mixed  $results
     * @return int
     */
    public function getTotalCount($results): int
    {
        return $results['total']['value'];
    }

    /**
     * Flush all of the model's records from the engine.
     *
     * @param  Model  $model
     * @return void
     */
    public function flush($model): void
    {
        $this->client->indices()->flush(['index' => $model->mappableAs()]);
    }
}
