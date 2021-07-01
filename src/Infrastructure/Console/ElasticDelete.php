<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\Console;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Console\Command;
use JeroenG\Explorer\Application\Explored;
use JeroenG\Explorer\Domain\IndexManagement\IndexConfigurationRepositoryInterface;
use JeroenG\Explorer\Infrastructure\Elastic\ElasticIndexAdapter;

class ElasticDelete extends Command
{
    protected $signature = 'elastic:delete';

    protected $description = 'Delete the Elastic indexes.';

    public function handle(ElasticIndexAdapter $adapter, IndexConfigurationRepositoryInterface $indexConfigurationRepository): int
    {
        $config = config('explorer');

        if (!$config) {
            $this->warn('There are no indexes defined!');

            return 1;
        }

        foreach ($indexConfigurationRepository->getConfigurations() as $config) {
            $adapter->delete($config);

            $this->info('Deleted index ' . $config->getName());
        }

        return 0;
    }
}
