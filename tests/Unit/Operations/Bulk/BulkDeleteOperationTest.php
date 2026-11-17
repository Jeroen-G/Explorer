<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Operations\Bulk;

use Generator;
use JeroenG\Explorer\Application\Operations\Bulk\BulkDeleteOperation;
use JeroenG\Explorer\Tests\Support\Models\TestModelWithException;
use JeroenG\Explorer\Tests\Support\Models\TestModelWithoutSettings;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BulkDeleteOperationTest extends TestCase
{
    public function test_it_builds_with_an_empty_command(): void
    {
        $operation = new BulkDeleteOperation(':searchable_as:');
        self::assertSame([], $operation->build());
    }

    public function test_it_builds_with_a_model_command(): void
    {
        $operation = new BulkDeleteOperation(':searchable_as:');
        $operation->add(new TestModelWithoutSettings());
        self::assertSame([
            ['delete' => [ '_index' => ':searchable_as:', '_id' => ':scout_key:' ]],
        ], $operation->build());
    }

    public function test_it_builds_with_multiple_model_command(): void
    {
        $operation = new BulkDeleteOperation(':searchable_as:');

        $operation->add(new TestModelWithoutSettings());
        $operation->add(new TestModelWithException());

        self::assertSame([
            ['delete' => [ '_index' => ':searchable_as:', '_id' => ':scout_key:' ]],
            ['delete' => [ '_index' => ':searchable_as:', '_id' => ':scout_key_exception:' ]],
        ], $operation->build());
    }

    #[DataProvider('iterableInputDataProvider')]
    public function test_it_builds_from_sources(iterable $input): void
    {
        $operation = BulkDeleteOperation::from($input, ':searchable_as:');

        self::assertSame([
            ['delete' => [ '_index' => ':searchable_as:', '_id' => ':scout_key:' ]],
        ], $operation->build());
    }

    public static function iterableInputDataProvider(): Generator
    {
        yield 'collection' => [collect([new TestModelWithoutSettings()])];
        yield 'array' => [[new TestModelWithoutSettings()]];
        yield 'iterator' => [new \ArrayIterator([new TestModelWithoutSettings()])];
    }

    public function test_it_keeps_the_index_name_per_operation(): void
    {
        $first = BulkDeleteOperation::from([new TestModelWithoutSettings()], ':first_index:');
        $second = BulkDeleteOperation::from([new TestModelWithoutSettings()], ':second_index:');

        self::assertSame([
            ['delete' => [ '_index' => ':first_index:', '_id' => ':scout_key:' ]],
        ], $first->build());
        self::assertSame([
            ['delete' => [ '_index' => ':second_index:', '_id' => ':scout_key:' ]],
        ], $second->build());
    }

    public function test_it_does_not_need_the_searchable_data_of_a_model(): void
    {
        $operation = new BulkDeleteOperation(':searchable_as:');
        $operation->add(new TestModelWithException('toSearchableArray'));

        self::assertSame([
            ['delete' => [ '_index' => ':searchable_as:', '_id' => ':scout_key_exception:' ]],
        ], $operation->build());
    }
}
