<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\Console;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Console\Command;
use JeroenG\Explorer\Application\Explored;

class ElasticDelete extends Command
{
    protected $signature = 'elastic:delete';

    protected $description = 'Delete the Elastic indexes.';

    private Client $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()->setHosts(config('explorer.connection'))->build();
        parent::__construct();
    }

    public function handle(): int
    {
        $config = config('explorer');

        if (!$config) {
            $this->warn('There are no indexes defined!');

            return 1;
        }

        foreach ($config['indexes'] as $name => $index) {
            $name = $this->getConfiguration($index) ?? $name;

            $this->deleteIndex($name);

            $this->info('Deleted index ' . $name);
        }

        return 0;
    }

    private function getConfiguration($index): ?string
    {
        if (!is_string($index) || !class_exists($index)) {
            return null;
        }

        $class = (new $index());

        if (!$class instanceof Explored) {
            return null;
        }

        return $class->searchableAs();
    }

    private function deleteIndex(string $name): void
    {
        $this->client->indices()->delete(['index' => $name]);
    }
}
