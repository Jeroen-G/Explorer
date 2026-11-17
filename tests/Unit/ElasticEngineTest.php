<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use JeroenG\Explorer\Application\DocumentAdapterInterface;
use JeroenG\Explorer\Application\Explored;
use JeroenG\Explorer\Application\IndexAdapterInterface;
use JeroenG\Explorer\Application\Operations\Bulk\BulkDeleteOperation;
use JeroenG\Explorer\Domain\IndexManagement\DirectIndexConfiguration;
use JeroenG\Explorer\Domain\IndexManagement\IndexConfigurationRepositoryInterface;
use JeroenG\Explorer\Infrastructure\Scout\ElasticEngine;
use JeroenG\Explorer\Tests\Support\Models\TestModelWithException;
use JeroenG\Explorer\Tests\Support\Models\TestModelWithoutSettings;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class ElasticEngineTest extends MockeryTestCase
{
    public function test_it_deletes_all_models_in_a_single_bulk_request(): void
    {
        $indexConfiguration = DirectIndexConfiguration::create(':searchable_as:', [], []);

        $repository = Mockery::mock(IndexConfigurationRepositoryInterface::class);
        $repository->expects('findForIndex')->with(':searchable_as:')->andReturn($indexConfiguration);

        $indexAdapter = Mockery::mock(IndexAdapterInterface::class);
        $indexAdapter->expects('ensureIndex')->with($indexConfiguration);

        $documentAdapter = Mockery::mock(DocumentAdapterInterface::class);
        $documentAdapter->expects('delete')->never();
        $documentAdapter
            ->expects('bulk')
            ->withArgs(static fn ($operation) => $operation instanceof BulkDeleteOperation && $operation->build() === [
                ['delete' => ['_index' => ':searchable_as:', '_id' => ':scout_key:']],
                ['delete' => ['_index' => ':searchable_as:', '_id' => ':scout_key_exception:']],
            ])
            ->andReturn([]);

        $engine = new ElasticEngine($indexAdapter, $documentAdapter, $repository);

        /** @var Collection<int, Model&Explored> $models */
        $models = new Collection([new TestModelWithoutSettings(), new TestModelWithException()]);

        $engine->delete($models);
    }

    public function test_it_does_not_send_a_request_when_there_is_nothing_to_delete(): void
    {
        $repository = Mockery::mock(IndexConfigurationRepositoryInterface::class);
        $repository->expects('findForIndex')->never();

        $indexAdapter = Mockery::mock(IndexAdapterInterface::class);
        $indexAdapter->expects('ensureIndex')->never();

        $documentAdapter = Mockery::mock(DocumentAdapterInterface::class);
        $documentAdapter->expects('bulk')->never();
        $documentAdapter->expects('delete')->never();

        $engine = new ElasticEngine($indexAdapter, $documentAdapter, $repository);

        $engine->delete(new Collection());
    }
}
