<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\Console;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Console\Command;
use JeroenG\Explorer\Domain\IndexManagement\IndexConfiguration;
use JeroenG\Explorer\Domain\IndexManagement\IndexConfigurationRepository;

class ElasticCreate extends Command
{
    protected $signature = 'elastic:create';

    protected $description = 'Create the Elastic indexes.';

    private Client $client;

    public function handle(IndexConfigurationRepository $indexConfigurationRepository): int
    {
        $this->client = ClientBuilder::create()->setHosts([config('explorer.connection')])->build();

        $config = config('explorer');
        if (!$config) {
            $this->warn('There are no indexes defined!');

            return 1;
        }

        foreach ($indexConfigurationRepository->getConfigurations() as $config) {
            $this->createIndex($config);

            $this->info('Created index ' . $config->name());
        }

        return 0;
    }

    private function createIndex(IndexConfiguration $indexConfiguration): void
    {
        $this->client->indices()->create([
            'index' => $indexConfiguration->name(),
            'body' => [
                'settings' => $indexConfiguration->settings(),
                'mappings' => [
                    'properties' => $indexConfiguration->properties()
                ]
            ]
        ]);
    }
}
