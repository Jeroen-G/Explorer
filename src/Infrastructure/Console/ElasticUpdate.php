<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\Console;

use Illuminate\Console\Command;
use JeroenG\Explorer\Application\IndexAdapterInterface;
use JeroenG\Explorer\Application\IndexChangedCheckerInterface;
use JeroenG\Explorer\Domain\IndexManagement\IndexConfigurationRepositoryInterface;

final class ElasticUpdate extends Command
{
    protected $signature = 'elastic:update';

    protected $description = 'Checks all indices and check if it needs to update them.';

    public function handle(
        IndexAdapterInterface $indexAdapter,
        IndexChangedCheckerInterface $changedChecker,
        IndexConfigurationRepositoryInterface $indexConfigurationRepository
    ): int
    {
        foreach ($indexConfigurationRepository->getConfigurations() as $config) {
            $isChanged = $changedChecker->check($config);

            if ($isChanged) {
                $this->output->writeln($config->getName() . ' is changed');
            }
        }
    }
}
