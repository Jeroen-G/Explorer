<?php

namespace JeroenG\Explorer\Domain\IndexManagement\Job;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use JeroenG\Explorer\Application\IndexAdapterInterface;
use JeroenG\Explorer\Domain\IndexManagement\IndexConfigurationRepositoryInterface;

class UpdateIndexAlias implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $index)
    {
    }

    public function handle(
        IndexAdapterInterface $indexAdapter,
        IndexConfigurationRepositoryInterface $indexConfigurationRepository
    ): void {
        $indexConfiguration = $indexConfigurationRepository->findForIndex($this->index);
        $indexAdapter->pointToAlias($indexConfiguration);
    }
}