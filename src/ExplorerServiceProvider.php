<?php

declare(strict_types=1);

namespace JeroenG\Explorer;

use Elasticsearch\ClientBuilder;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use JeroenG\Explorer\Application\DocumentAdapterInterface;
use JeroenG\Explorer\Application\IndexAdapterInterface;
use JeroenG\Explorer\Application\IndexChangedCheckerInterface;
use JeroenG\Explorer\Domain\Aggregations\AggregationSyntaxInterface;
use JeroenG\Explorer\Domain\IndexManagement\IndexConfigurationRepositoryInterface;
use JeroenG\Explorer\Infrastructure\Console\ElasticCreate;
use JeroenG\Explorer\Infrastructure\Console\ElasticDelete;
use JeroenG\Explorer\Infrastructure\Console\ElasticSearch;
use JeroenG\Explorer\Infrastructure\Console\ElasticUpdate;
use JeroenG\Explorer\Infrastructure\Elastic\ElasticAdapter;
use JeroenG\Explorer\Infrastructure\Elastic\ElasticClientFactory;
use JeroenG\Explorer\Infrastructure\Elastic\ElasticDocumentAdapter;
use JeroenG\Explorer\Infrastructure\Elastic\ElasticIndexAdapter;
use JeroenG\Explorer\Infrastructure\IndexManagement\ElasticIndexChangedChecker;
use JeroenG\Explorer\Infrastructure\IndexManagement\ElasticIndexConfigurationRepository;
use JeroenG\Explorer\Infrastructure\Scout\ElasticEngine;
use Laravel\Scout\Builder;
use Laravel\Scout\EngineManager;

class ExplorerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        $this->app->bind(ElasticClientFactory::class, function () {
            $client = ClientBuilder::create()->setHosts([config('explorer.connection')]);

            if(config()->has('explorer.connection.api')) {
                $client->setApiKey(
                    config('explorer.connection.api.id'),
                    config('explorer.connection.api.key')
                );
            }

            return new ElasticClientFactory($client->build());
        });

        $this->app->bind(IndexAdapterInterface::class, ElasticIndexAdapter::class);

        $this->app->bind(DocumentAdapterInterface::class, ElasticDocumentAdapter::class);

        $this->app->bind(IndexChangedCheckerInterface::class, ElasticIndexChangedChecker::class);

        $this->app->bind(DeprecatedElasticAdapterInterface::class, function () {
            $client = ClientBuilder::create()->setHosts([config('explorer.connection')])->build();
            return new ElasticAdapter($client);
        });

        $this->app->bind(IndexConfigurationRepositoryInterface::class, function () {
            return new ElasticIndexConfigurationRepository(
                config('explorer.indexes') ?? [],
                config('explorer.prune_old_aliases')
            );
        });

        resolve(EngineManager::class)->extend('elastic', function (Application $app) {
            return new ElasticEngine(
                $app->make(IndexAdapterInterface::class),
                $app->make(DocumentAdapterInterface::class),
                $app->make(IndexConfigurationRepositoryInterface::class)
            );
        });

        Builder::macro('must', function ($must) {
            $this->must[] = $must;
            return $this;
        });

        Builder::macro('should', function ($should) {
            $this->should[] = $should;
            return $this;
        });

        Builder::macro('filter', function ($filter) {
            $this->filter[] = $filter;
            return $this;
        });

        Builder::macro('field', function (string $field) {
            $this->fields[] = $field;
            return $this;
        });

        Builder::macro('newCompound', function ($compound) {
            $this->compound = $compound;
            return $this;
        });

        Builder::macro('aggregation', function (string $name, AggregationSyntaxInterface $aggregation) {
            $this->aggregations[$name] = $aggregation;
            return $this;
        });
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/explorer.php', 'explorer');
    }

    public function provides(): array
    {
        return ['explorer'];
    }

    protected function bootForConsole(): void
    {
        $this->publishes([
            __DIR__ . '/../config/explorer.php' => config_path('explorer.php'),
        ], 'explorer.config');

        $this->commands([
             ElasticCreate::class,
             ElasticDelete::class,
             ElasticSearch::class,
             ElasticUpdate::class,
         ]);
    }
}
