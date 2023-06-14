<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\Console;

use Illuminate\Bus\Dispatcher;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use JeroenG\Explorer\Application\IndexAdapterInterface;
use JeroenG\Explorer\Domain\IndexManagement\AliasedIndexConfiguration;
use JeroenG\Explorer\Domain\IndexManagement\IndexConfigurationInterface;
use JeroenG\Explorer\Domain\IndexManagement\IndexConfigurationRepositoryInterface;
use JeroenG\Explorer\Infrastructure\IndexManagement\Job\UpdateIndexAlias;

final class ElasticUpdate extends Command
{
    protected $signature = 'elastic:update {index?}';

    protected $description = 'Checks all indices and check if it needs to update them.';

    public function handle(
        IndexAdapterInterface $indexAdapter,
        IndexConfigurationRepositoryInterface $indexConfigurationRepository,
        Dispatcher $dispatcher
    ): int {
        $index = $this->argument('index');

        /** @var IndexConfigurationInterface[] $allConfigs */
        $allConfigs = is_null($index) ?
            $indexConfigurationRepository->getConfigurations() : [$indexConfigurationRepository->findForIndex($index)];

        foreach ($allConfigs as $config) {
            $this->updateIndex($config, $indexAdapter, $dispatcher);
        }

        return 0;
    }

    private function updateIndex(
        IndexConfigurationInterface $indexConfiguration,
        IndexAdapterInterface $indexAdapter,
        Dispatcher $dispatcher,
    ): void {
        if ($indexConfiguration instanceof AliasedIndexConfiguration) {
            $indexAdapter->createNewWriteIndex($indexConfiguration);
        }

        if (!is_null($indexConfiguration->getModel())) {
            $output = Artisan::call('scout:import', ["model" => $indexConfiguration->getModel()], $this->output);

            if ($output !== 0) {
                $this->error(sprintf("Import of model %s failed", $indexConfiguration->getModel()));
                return;
            }
        }

        $dispatcher->dispatch(
            UpdateIndexAlias::createFor($indexConfiguration)
        );
    }
}
