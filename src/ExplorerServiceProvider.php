<?php

namespace JeroenG\Explorer;

use Illuminate\Support\ServiceProvider;
use JeroenG\Explorer\Infrastructure\Console\CreateCommand;
use JeroenG\Explorer\Infrastructure\Console\DeleteCommand;
use JeroenG\Explorer\Infrastructure\Console\SearchCommand;
use JeroenG\Explorer\Infrastructure\ElasticEngine;
use Laravel\Scout\Builder;
use Laravel\Scout\EngineManager;

class ExplorerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        resolve(EngineManager::class)->extend('elastic', function () {
            return new ElasticEngine();
        });

        Builder::macro('must', function ($must) {
            $this->musts[] = $must;
            return $this;
        });

        Builder::macro('should', function ($should) {
            $this->shoulds[] = $should;
            return $this;
        });

        Builder::macro('filter', function ($filter) {
            $this->filters[] = $filter;
            return $this;
        });
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/explorer.php', 'explorer');
    }

    public function provides(): array
    {
        return ['explorer'];
    }

    protected function bootForConsole(): void
    {
        $this->publishes([
            __DIR__.'/../config/explorer.php' => config_path('explorer.php'),
        ], 'explorer.config');

         $this->commands([
             CreateCommand::class,
             DeleteCommand::class,
             SearchCommand::class,
         ]);
    }
}
