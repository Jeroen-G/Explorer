<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\Scout;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use JeroenG\Explorer\Application\IndexAdapterInterface;
use JeroenG\Explorer\Application\Operations\Bulk\BulkUpdateOperation;
use JeroenG\Explorer\Application\Results;
use Laravel\Scout\Builder;
use Laravel\Scout\Engines\Engine;
use Webmozart\Assert\Assert;

class ElasticEngine extends Engine
{
    private IndexAdapterInterface $adapter;

    public function __construct(IndexAdapterInterface  $adapter)
    {
        $this->adapter = $adapter;
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

        $operation = BulkUpdateOperation::from($models);
        $this->adapter->bulk($operation);
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

        $models->each(function ($model) {
            $this->adapter->delete($model->searchableAs(), $model->getScoutKey());
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
        $normalizedBuilder = ScoutSearchCommandBuilder::wrap($builder);
        return $this->adapter->search($normalizedBuilder);
    }

    /**
     * Perform the given search on the engine.
     *
     * @param Builder $builder
     * @param  int  $perPage
     * @param  int  $page
     * @return Results
     */
    public function paginate(Builder $builder, $perPage, $page): Results
    {
        $limit = $perPage;
        $offset = $limit * $perPage;

        $normalizedBuilder = ScoutSearchCommandBuilder::wrap($builder);
        $normalizedBuilder->setOffset($offset);
        $normalizedBuilder->setLimit($limit);
        return $this->adapter->search($normalizedBuilder);
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
            $builder,
            $objectIds
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
        $this->adapter->flush($model->searchableAs());
    }
}
