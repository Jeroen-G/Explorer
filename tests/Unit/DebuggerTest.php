<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit;

use JeroenG\Explorer\Infrastructure\Scout\Debugger;
use PHPUnit\Framework\TestCase;

class DebuggerTest extends TestCase
{
    public function test_it_can_return_query_as_array_or_json(): void
    {
        $debugger = new Debugger(['query' => []]);

        $expectedJson = "{\n    \"query\": []\n}";

        self::assertSame(['query' => []], $debugger->array());
        self::assertSame($expectedJson, $debugger->json());
    }
}
