<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Operations\Bulk;

use JeroenG\Explorer\Application\Operations\Bulk\BulkUpdateOperation;
use JeroenG\Explorer\Tests\Support\Models\TestModelWithoutSettings;
use JeroenG\Explorer\Tests\Support\Models\TestModelWithSettings;
use PHPUnit\Framework\TestCase;

class BulkUpdateOperationTest extends TestCase
{
    public function testItBuildsEmptyCommand(): void
    {
        $operation = new BulkUpdateOperation();
        self::assertEquals([], $operation->build());
    }

    public function testItBuildsWithModelCommand(): void
    {
        $operation = new BulkUpdateOperation();
        $operation->add(new TestModelWithoutSettings());
        self::assertEquals([
            ['index' => [ '_index' => ':searchable_as:', '_id' => ':scout_key:' ]],
            [ 'data' => true ]
        ], $operation->build());
    }

    public function testItBuildsWithMultipleModelsCommand(): void
    {
        $operation = new BulkUpdateOperation();

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
    public function testItBuildsFromSources($input): void
    {
        $operation = BulkUpdateOperation::from($input);

        self::assertEquals([
            ['index' => [ '_index' => ':searchable_as:', '_id' => ':scout_key:' ]],
            [ 'data' => true ]
        ], $operation->build());
    }

    public function iterableInputDataProvider()
    {
        yield 'collection' => [collect([new TestModelWithoutSettings()])];
        yield 'array' => [[new TestModelWithoutSettings()]];
        yield 'iterator' => [new \ArrayIterator([new TestModelWithoutSettings()])];
    }
}
