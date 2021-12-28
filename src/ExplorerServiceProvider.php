<?php

declare(strict_types=1);

namespace JeroenG\Explorer;

use Elasticsearch\ClientBuilder;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use JeroenG\Explorer\Application\DocumentAdapterInterface;
use JeroenG\Explorer\Application\IndexAdapterInterface;
use JeroenG\Explorer\Domain\Aggregations\AggregationSyntaxInterface;
use JeroenG\Explorer\Domain\IndexManagement\IndexConfigurationRepositoryInterface;
use JeroenG\Explorer\Infrastructure\Console\ElasticCreate;
use JeroenG\Explorer\Infrastructure\Console\ElasticDelete;
use JeroenG\Explorer\Infrastructure\Console\ElasticSearch;
use JeroenG\Explorer\Infrastructure\Elastic\ElasticAdapter;
use JeroenG\Explorer\Infrastructure\Elastic\ElasticClientFactory;
use JeroenG\Explorer\Infrastructure\Elastic\ElasticDocumentAdapter;
use JeroenG\Explorer\Infrastructure\Elastic\ElasticIndexAdapter;
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
            // Connect to ES
            $connection = config('explorer.connection');
            $client = ClientBuilder::create()->setHosts([$connection]);
            $api = config('explorer.api');
            // Check if cloud-id is required
            $cloud_id = array_key_exists('cloud-id', $api) && $api['cloud-id'] !== '';
            if ($cloud_id) {
                $client = $client->setElasticCloudId($api['cloud-id']);
            }
            // Check if api-key is required
            $id = array_key_exists('id', $api) && $api['id'] !== '';
            $key = array_key_exists('key', $api) && $api['key'] !== '';
            if ($id && $key) {
                $client = $client->setApiKey($api['id'], $api['key']);
            }
            // Build client
            $client = $client->build();
            return new ElasticClientFactory($client);
        });

        $this->app->bind(IndexAdapterInterface::class, ElasticIndexAdapter::class);

        $this->app->bind(DocumentAdapterInterface::class, ElasticDocumentAdapter::class);

        $this->app->bind(DeprecatedElasticAdapterInterface::class, function () {
            // Connect to ES
            $connection = config('explorer.connection');
            $client = ClientBuilder::create()->setHosts([$connection]);
            $api = config('explorer.api');
            // Check if cloud-id is required
            $cloud_id = array_key_exists('cloud-id', $api) && $api['cloud-id'] !== '';
            if ($cloud_id) {
                $client = $client->setElasticCloudId($api['cloud-id']);
            }
            // Check if api-key is required
            $id = array_key_exists('id', $api) && $api['id'] !== '';
            $key = array_key_exists('key', $api) && $api['key'] !== '';
            if ($id && $key) {
                $client = $client->setApiKey($api['id'], $api['key']);
            }
            // Build client
            $client = $client->build();
            return new ElasticAdapter($client);
        });

        $this->app->bind(IndexConfigurationRepositoryInterface::class, function () {
            return new ElasticIndexConfigurationRepository(config('explorer.indexes') ?? []);
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
         ]);
    }
}
