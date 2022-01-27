<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\Scout;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use JeroenG\Explorer\Application\DocumentAdapterInterface;
use JeroenG\Explorer\Application\Explored;
use JeroenG\Explorer\Application\IndexAdapterInterface;
use JeroenG\Explorer\Application\Operations\Bulk\BulkUpdateOperation;
use JeroenG\Explorer\Application\Results;
use JeroenG\Explorer\Application\Transformers\ElasticHitsToCollectionTransformer;
use JeroenG\Explorer\Application\Transformers\ElasticHitsToLazyCollectionTransformer;
use JeroenG\Explorer\Domain\IndexManagement\IndexConfigurationRepositoryInterface;
use Laravel\Scout\Builder;
use Laravel\Scout\Engines\Engine;
use Laravel\Scout\Searchable;
use Webmozart\Assert\Assert;

class ElasticEngine extends Engine
{
    private IndexConfigurationRepositoryInterface $indexConfigurationRepository;

    private IndexAdapterInterface $indexAdapter;

    private DocumentAdapterInterface $documentAdapter;

    private static ?array $lastQuery;

    public function __construct(
        IndexAdapterInterface $indexAdapter,
        DocumentAdapterInterface $documentAdapter,
        IndexConfigurationRepositoryInterface $indexConfigurationRepository
    )
    {
        $this->indexAdapter = $indexAdapter;
        $this->documentAdapter = $documentAdapter;
        $this->indexConfigurationRepository = $indexConfigurationRepository;
    }

    /**
     * Update the given model in the index.
     * The index is deduced from the first model in the collection.
     *
     * @param \Illuminate\Database\Eloquent\Collection $models
     * @return void
     */
    public function update($models): void
    {
        if ($models->isEmpty()) {
            return;
        }

        /** @var Explored $firstModel */
        $firstModel = $models->first();

        $indexConfiguration = $this->indexConfigurationRepository->findForIndex($firstModel->searchableAs());
        $indexName = $indexConfiguration->getWriteIndexName();

        $this->documentAdapter->bulk(BulkUpdateOperation::from($models, $indexName));
    }

    /**
     * Remove the given model from the index.
     *
     * @param \Illuminate\Database\Eloquent\Collection $models
     * @return void
     */
    public function delete($models): void
    {
        if ($models->isEmpty()) {
            return;
        }

        $firstModel = $models->first();
        $indexConfiguration = $this->indexConfigurationRepository->findForIndex($firstModel->searchableAs());
        $indexName = $indexConfiguration->getWriteIndexName();

        $models->each(function ($model) use ($indexName) {
            $this->documentAdapter->delete($indexName, $model->getScoutKey());
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
        self::$lastQuery = $normalizedBuilder->buildQuery();
        return $this->documentAdapter->search($normalizedBuilder);
    }

    /**
     * Perform the given search on the engine.
     *
     * @param Builder $builder
     * @param int $perPage
     * @param int $page
     * @return Results
     */
    public function paginate(Builder $builder, $perPage, $page): Results
    {
        $offset = $perPage * ($page - 1);

        $normalizedBuilder = ScoutSearchCommandBuilder::wrap($builder);
        $normalizedBuilder->setOffset($offset);
        $normalizedBuilder->setLimit($perPage);
        self::$lastQuery = $normalizedBuilder->buildQuery();
        return $this->documentAdapter->search($normalizedBuilder);
    }

    /**
     * Pluck and return the primary keys of the given results.
     *
     * @param mixed $results
     * @return Collection
     */
    public function mapIds($results): Collection
    {
        Assert::isInstanceOf($results, Results::class);

        return collect($results->hits())->pluck('_id')->values();
    }

    private function hasModelClassName($results)
    {
        Assert::isInstanceOf($results, Results::class);

        $first = collect($results->hits())->first();
        if (null === $first) {
            return false;
        }

        if (false === isset($first['_source']['__class_name'])) {
            return false;
        }

        return true;
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

        if ($results->count() === 0) {
            return collect();
        }
        $hits = collect($results->hits());

        if (false === $this->hasModelClassName($results)) {
            $data = $model->getScoutModelsByIds(
                $builder,
                $objectIds
            );
        } else {
            $data = $hits->groupBy('_source.__class_name')
                ->map(function ($results, $class) use ($builder) {
                    $model = new $class;
                    /* @var Searchable $model */
                    return $models = $model->getScoutModelsByIds(
                        $builder, $results->pluck('_id')->all()
                    );
                })
                ->flatten();
        }

        return $data->filter(function ($model) use ($objectIds) {
            return in_array($model->getScoutKey(), $objectIds, false);
        })->sortBy(function ($model) use ($objectIdPositions) {
            return $objectIdPositions[$model->getScoutKey()];
        })->values();
    }

    public function lazyMap(Builder $builder, $results, $model): LazyCollection
    {
        Assert::isInstanceOf($results, Results::class);

        if ($results->count() === 0) {
            return LazyCollection::make($model->newCollection());
        }

        $objectIds = $this->mapIds($results)->all();
        $objectIdPositions = array_flip($objectIds);

        if(false === $this->hasModelClassName($results)){
            $data = $model->getScoutModelsByIds(
                $builder,
                $objectIds
            )->cursor();
        }else {
            $data = LazyCollection::make(function () use ($results, $builder) {
                foreach ($results->hits() as $hit) {
                    $class = $hit['_source']['__class_name'];

                    $model = new $class;

                    /* @var Searchable $model */
                    yield $model->getScoutModelsByIds(
                        $builder, [$hit['_id']]
                    )->first();
                }
            });
        }

        return $data->filter(function ($model) use ($objectIds) {
            return in_array($model->getScoutKey(), $objectIds, false);
        })->sortBy(function ($model) use ($objectIdPositions) {
            return $objectIdPositions[$model->getScoutKey()];
        })->values();
    }

    /**
     * Get the total count from a raw result returned by the engine.
     *
     * @param Results $results
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
     * @param Model $model
     * @return void
     */
    public function flush($model): void
    {
        $this->documentAdapter->flush($model->searchableAs());
    }

    public static function debug(): Debugger
    {
        return new Debugger(self::$lastQuery ?? []);
    }

    public function createIndex($name, array $options = []): void
    {
        $configuration = $this->indexConfigurationRepository->findForIndex($name);
        $this->indexAdapter->create($configuration);
    }

    public function deleteIndex($name): void
    {
        $configuration = $this->indexConfigurationRepository->findForIndex($name);
        $this->indexAdapter->delete($configuration);
    }
}
