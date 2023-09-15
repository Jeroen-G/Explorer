<?php declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\IndexManagement\Job;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use JeroenG\Explorer\Application\IndexAdapterInterface;
use JeroenG\Explorer\Domain\IndexManagement\IndexConfigurationInterface;
use JeroenG\Explorer\Domain\IndexManagement\IndexConfigurationRepositoryInterface;

final class UpdateIndexAlias implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private function __construct(public string $index)
    {
    }

    public static function createFor(IndexConfigurationInterface $indexConfiguration): self
    {
        $modelClassName = $indexConfiguration->getModel();
        $model = new $modelClassName();

        return (new self($indexConfiguration->getName()))
            ->onQueue($model->syncWithSearchUsingQueue())
            ->onConnection($model->syncWithSearchUsing());
    }

    public function handle(
        IndexAdapterInterface $indexAdapter,
        IndexConfigurationRepositoryInterface $indexConfigurationRepository
    ): void {
        $indexConfiguration = $indexConfigurationRepository->findForIndex($this->index);
        $indexAdapter->pointToAlias($indexConfiguration);
    }
}