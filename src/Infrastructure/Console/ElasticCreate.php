<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\Console;

use Illuminate\Console\Command;
use JeroenG\Explorer\Domain\IndexManagement\IndexConfigurationRepositoryInterface;
use JeroenG\Explorer\Infrastructure\Elastic\ElasticIndexAdapter;

class ElasticCreate extends Command
{
    protected $signature = 'elastic:create';

    protected $description = 'Create the Elastic indexes.';

    public function handle(ElasticIndexAdapter $adapter, IndexConfigurationRepositoryInterface $indexConfigurationRepository): int
    {
        $this->warn('This command is deprecated and will be removed in 3.0. Use scout:index `name` instead.');

        $config = config('explorer');
        if (!$config) {
            $this->warn('There are no indices defined!');

            return 1;
        }

        foreach ($indexConfigurationRepository->getConfigurations() as $config) {
            $adapter->create($config);

            $this->info('Created index ' . $config->getName());
        }

        return 0;
    }
}
