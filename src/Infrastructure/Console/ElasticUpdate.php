<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use JeroenG\Explorer\Application\IndexAdapterInterface;
use JeroenG\Explorer\Application\IndexChangedCheckerInterface;
use JeroenG\Explorer\Domain\IndexManagement\IndexConfigurationInterface;
use JeroenG\Explorer\Domain\IndexManagement\IndexConfigurationRepositoryInterface;

final class ElasticUpdate extends Command
{
    protected $signature = 'elastic:update {index?} {--force}';

    protected $description = 'Checks all indices and check if it needs to update them.';

    public function handle(
        IndexAdapterInterface $indexAdapter,
        IndexChangedCheckerInterface $changedChecker,
        IndexConfigurationRepositoryInterface $indexConfigurationRepository
    ): int {
        $index = $this->argument('index');
        $isForced = $this->option('force');

        /** @var IndexConfigurationInterface $allConfigs */
        $allConfigs = is_null($index) ?
            $indexConfigurationRepository->getConfigurations() : $indexConfigurationRepository->findForIndex($index);


        $configsToUpdate = collect($allConfigs)->filter(
            fn (IndexConfigurationInterface $config) => $isForced || (!is_null($config->getModel()) && $changedChecker->check($config))
        );

        foreach ($configsToUpdate as $config) {
            $this->updateIndex($config, $indexAdapter);
        }

        return 0;
    }

    private function updateIndex(
        IndexConfigurationInterface $indexConfiguration,
        IndexAdapterInterface $indexAdapter
    ): void {
        $indexAdapter->createNewInactiveIndex($indexConfiguration);

        if (!is_null($indexConfiguration->getModel())) {
            $output = Artisan::call('scout:import', ["model" => $indexConfiguration->getModel()]);

            if ($output !== 0) {
                $this->error(sprintf("Import of model %s failed", $indexConfiguration->getModel()));
                return;
            }
        }

        $indexAdapter->pointToAlias($indexConfiguration);
    }
}
