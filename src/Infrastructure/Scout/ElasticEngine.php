<?php

namespace JeroenG\Explorer\Infrastructure\Scout;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use JeroenG\Explorer\Application\Explored;
use JeroenG\Explorer\Application\Finder;
use JeroenG\Explorer\Application\BuildCommand;
use JeroenG\Explorer\Application\Results;
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
                'index' => $model->searchableAs(),
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
                'index' => $model->searchableAs(),
                'id' => $model->getScoutKey(),
            ];

            $this->client->delete($data);
        });
    }

    /**
     * Perform the given search on the engine.
     *
     * @param Builder $builder
     * @return Results
     */
    public function search(Builder $builder): Results
    {
        return $this->executeSearch($builder);
    }

    private function executeSearch(Builder $builder): Results
    {
        $normalizedBuilder = BuildCommand::wrap($builder);
        $finder = new Finder($this->client, $normalizedBuilder);
        return $finder->find();
    }

    /**
     * Perform the given search on the engine.
     *
     * @param Builder $builder
     * @param  int  $perPage
     * @param  int  $page
     * @return Results
     */
    public function paginate(Builder $builder, $perPage, $page)
    {
        // TODO[explorer]: implement pagination
        return $this->executeSearch($builder);
    }

    /**
     * Pluck and return the primary keys of the given results.
     *
     * @param  mixed  $results
     * @return Collection
     */
    public function mapIds($results): Collection
    {
        Assert::isInstanceOf($results, Results::class);

        return collect($results->hits())->pluck('_id')->values();
    }

    /**
     * Map the given results to instances of the given model.
     *
     * @param Builder $builder
     * @param  Results  $results
     * @param  Model  $model
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function map(Builder $builder, $results, $model): Collection
    {
        Assert::isInstanceOf($results, Results::class);

        if ($results->count() === 0) {
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
     * @param  Results  $results
     * @return int
     */
    public function getTotalCount($results): int
    {
        Assert::isInstanceOf($results, Results::class);

        return $results->count();
    }

    /**
     * Flush all of the model's records from the engine.
     *
     * @param  Model  $model
     * @return void
     */
    public function flush($model): void
    {
        $this->client->indices()->flush(['index' => $model->searchableAs()]);
    }
}
