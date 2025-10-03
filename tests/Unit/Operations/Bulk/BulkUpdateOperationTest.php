<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Operations\Bulk;

use JeroenG\Explorer\Application\Operations\Bulk\BulkUpdateOperation;
use JeroenG\Explorer\Tests\Support\Models\TestModelWithException;
use JeroenG\Explorer\Tests\Support\Models\TestModelWithoutSettings;
use JeroenG\Explorer\Tests\Support\Models\TestModelWithPrepare;
use JeroenG\Explorer\Tests\Support\Models\TestModelWithSettings;
use JeroenG\Explorer\Tests\Support\TestLogger;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class BulkUpdateOperationTest extends TestCase
{
    public function test_it_builds_with_an_empty_command(): void
    {
        $operation = new BulkUpdateOperation(':searchable_as:', new NullLogger());
        self::assertEquals([], $operation->build());
    }

    public function test_it_builds_with_a_model_command(): void
    {
        $operation = new BulkUpdateOperation(':searchable_as:', new NullLogger());
        $operation->add(new TestModelWithoutSettings());
        self::assertEquals([
            ['index' => [ '_index' => ':searchable_as:', '_id' => ':scout_key:' ]],
            [ 'data' => true ]
        ], $operation->build());
    }

    public function test_it_builds_with_multiple_model_command(): void
    {
        $operation = new BulkUpdateOperation(':searchable_as:', new NullLogger());

        $operation->add(new TestModelWithoutSettings());
        $operation->add(new TestModelWithSettings());

        self::assertEquals([
            ['index' => [ '_index' => ':searchable_as:', '_id' => ':scout_key:' ]],
            [ 'data' => true ],

            ['index' => [ '_index' => ':searchable_as:', '_id' => ':scout_key:' ]],
            [ 'data' => true ]
        ], $operation->build());
    }

    /**
     * @dataProvider iterableInputDataProvider
     */
    public function test_it_builds_from_sources($input): void
    {
        $operation = BulkUpdateOperation::from($input, ':searchable_as:', new NullLogger());

        self::assertEquals([
            ['index' => [ '_index' => ':searchable_as:', '_id' => ':scout_key:' ]],
            [ 'data' => true ]
        ], $operation->build());
    }

    public static function iterableInputDataProvider(): \Generator
    {
        yield 'collection' => [collect([new TestModelWithoutSettings()])];
        yield 'array' => [[new TestModelWithoutSettings()]];
        yield 'iterator' => [new \ArrayIterator([new TestModelWithoutSettings()])];
    }

    public function test_it_builds_with_preparation_of_model(): void
    {
        $operation = new BulkUpdateOperation(':searchable_as:', new NullLogger());
        $operation->add(new TestModelWithPrepare());
        self::assertEquals([
            ['index' => [ '_index' => ':searchable_as:', '_id' => ':scout_key:' ]],
            [ 'data' => true, 'extra' => true ]
        ], $operation->build());
    }

    public function test_it_logs_error_when_to_searchable_array_throws_exception(): void
    {
        $logger = new TestLogger();
        $operation = new BulkUpdateOperation(':searchable_as:', $logger);
        
        $model = new TestModelWithException('toSearchableArray');
        $operation->add($model);
        
        $result = $operation->build();
        
        // Should return empty array for the failing model but continue processing
        self::assertEquals([
            ['index' => [ '_index' => ':searchable_as:', '_id' => ':scout_key_exception:' ]],
            []
        ], $result);
        
        // Check that error was logged
        self::assertTrue($logger->hasErrorRecords());
        $errorRecord = $logger->records[0];
        self::assertEquals('error', $errorRecord['level']);
        self::assertEquals('Error in toSearchableArray() or prepare() method', $errorRecord['message']);
        self::assertArrayHasKey('model_class', $errorRecord['context']);
        self::assertArrayHasKey('model_key', $errorRecord['context']);
        self::assertArrayHasKey('index', $errorRecord['context']);
        self::assertArrayHasKey('error', $errorRecord['context']);
        self::assertEquals('Error in toSearchableArray method', $errorRecord['context']['error']);
    }
}
