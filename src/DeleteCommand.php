<?php

namespace JeroenG\Explorer;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Console\Command;

class DeleteCommand extends Command
{
    protected $signature = 'elastic:delete';

    protected $description = 'Delete the Elastic indexes.';

    private Client $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()->build();
        parent::__construct();
    }

    public function handle(): int
    {
        $config = config('explorer.elastic');

        if (!$config) {
            $this->warn('There are no indexes defined!');

            return 1;
        }

        foreach ($config['indexes'] as $name => $index) {
            $name = $this->getConfiguration($index) ?? $name;

            $this->deleteIndex($name);

            $this->info('Deleted index '.$name);
        }

        return 0;
    }

    private function getConfiguration($index): ?string
    {
        if (!class_exists($index)) {
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
