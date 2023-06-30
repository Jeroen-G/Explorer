<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\IndexManagement\Job;

use JeroenG\Explorer\Application\IndexAdapterInterface;
use JeroenG\Explorer\Domain\IndexManagement\DirectIndexConfiguration;
use JeroenG\Explorer\Domain\IndexManagement\IndexConfigurationRepositoryInterface;
use JeroenG\Explorer\Infrastructure\IndexManagement\Job\UpdateIndexAlias;
use JeroenG\Explorer\Tests\Support\Models\SyncableModel;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHPUnit\Framework\Assert;

final class UpdateIndexAliasTest extends MockeryTestCase
{
    public function testJobCreation(): void
    {
        $config = DirectIndexConfiguration::create(
            name: ':index:',
            properties: [],
            settings: [],
            model: SyncableModel::class,
        );

        $subject = UpdateIndexAlias::createFor($config);

        Assert::assertSame(':queue:', $subject->queue);
        Assert::assertSame(':connection:', $subject->connection);
        Assert::assertSame(':index:', $subject->index);

    }

    public function testHandleCallsPointToIndex(): void
    {
        $adapter = Mockery::mock(IndexAdapterInterface::class);
        $repository = Mockery::mock(IndexConfigurationRepositoryInterface::class);

        $config = DirectIndexConfiguration::create(
            name: ':index:',
            properties: [],
            settings: [],
            model: SyncableModel::class,
        );

        $repository
            ->expects('findForIndex')
            ->with(':index:')
            ->andReturn($config);

        $adapter
            ->expects('pointToAlias')
            ->with($config);

        UpdateIndexAlias::createFor($config)
            ->handle($adapter, $repository);

    }
}
