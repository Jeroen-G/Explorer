<?php

declare(strict_types=1);

namespace JeroenG\Explorer;

use Elasticsearch\ClientBuilder;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use JeroenG\Explorer\Application\IndexAdapterInterface;
use JeroenG\Explorer\Domain\IndexManagement\IndexConfigurationRepositoryInterface;
use JeroenG\Explorer\Infrastructure\Console\ElasticCreate;
use JeroenG\Explorer\Infrastructure\Console\ElasticDelete;
use JeroenG\Explorer\Infrastructure\Console\ElasticSearch;
use JeroenG\Explorer\Infrastructure\Elastic\ElasticAdapter;
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

        $this->app->bind(IndexAdapterInterface::class, function () {
            $client = ClientBuilder::create()->setHosts([config('explorer.connection')])->build();
            return new ElasticAdapter($client);
        });
        resolve(EngineManager::class)->extend('elastic', function (Application $app) {
            return new EngineManager($app->make(IndexAdapterInterface::class));
        });
        $this->app->bind(IndexConfigurationRepositoryInterface::class, function () {
            return new ElasticIndexConfigurationRepository(config('explorer.indexes') ?? []);
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
         ]);
    }
}
