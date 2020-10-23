<?php

namespace JeroenG\Explorer;

use Illuminate\Support\ServiceProvider;
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
