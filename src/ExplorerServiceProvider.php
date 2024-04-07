<?php

declare(strict_types=1);

namespace JeroenG\Explorer;

use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\ClientInterface;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use JeroenG\Explorer\Application\DocumentAdapterInterface;
use JeroenG\Explorer\Application\IndexAdapterInterface;
use JeroenG\Explorer\Domain\IndexManagement\IndexConfigurationRepositoryInterface;
use JeroenG\Explorer\Infrastructure\Console\ElasticSearch;
use JeroenG\Explorer\Infrastructure\Console\ElasticUpdate;
use JeroenG\Explorer\Infrastructure\Elastic\ElasticDocumentAdapter;
use JeroenG\Explorer\Infrastructure\Elastic\ElasticIndexAdapter;
use JeroenG\Explorer\Infrastructure\IndexManagement\ElasticIndexConfigurationRepository;
use JeroenG\Explorer\Infrastructure\Scout\Builder;
use JeroenG\Explorer\Infrastructure\Scout\ElasticEngine;
use Laravel\Scout\EngineManager;

/**
 * @property array $must
 * @property array $should
 * @property array $filter
 * @property array $fields
 * @property array $compound
 * @property array $aggregations
 * @property array $queryProperties
 */
#[\AllowDynamicProperties]
class ExplorerServiceProvider extends ServiceProvider
{
    public array $bindings = [
        IndexAdapterInterface::class => ElasticIndexAdapter::class,
        DocumentAdapterInterface::class => ElasticDocumentAdapter::class,
        IndexConfigurationRepositoryInterface::class => ElasticIndexConfigurationRepository::class,
        \Laravel\Scout\Builder::class => Builder::class,
    ];

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        // Removed the old client builder and factory in favor of the native elastic client builder for simplicity
        // This if statement allows the user to define their own client implementation outside of this package
        if(!$this->app->has(ClientInterface::class)){
            $this->app->singleton(ClientInterface::class, fn (Application $app) => ClientBuilder::fromConfig(config('explorer.connection')));
        }

        $this->app->when(ElasticIndexConfigurationRepository::class)
            ->needs('$indexConfigurations')
            ->give(config('explorer.indexes') ?? []);

        $this->app->when(ElasticIndexConfigurationRepository::class)
            ->needs('$pruneOldAliases')
            ->give(config('explorer.prune_old_aliases') ?? true);

        $this->app->when(ElasticIndexConfigurationRepository::class)
            ->needs('$defaultSettings')
            ->give(config('explorer.default_index_settings') ?? []);

        resolve(EngineManager::class)->extend('elastic', function (Application $app) {
            return new ElasticEngine(
                $app->make(IndexAdapterInterface::class),
                $app->make(DocumentAdapterInterface::class),
                $app->make(IndexConfigurationRepositoryInterface::class)
            );
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
             ElasticSearch::class,
             ElasticUpdate::class,
         ]);
    }
}
