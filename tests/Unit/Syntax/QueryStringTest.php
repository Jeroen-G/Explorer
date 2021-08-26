<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Syntax;

use JeroenG\Explorer\Domain\Syntax\Nested;
use JeroenG\Explorer\Domain\Syntax\QueryString;
use JeroenG\Explorer\Domain\Syntax\Term;
use PHPUnit\Framework\TestCase;

class QueryStringTest extends TestCase
{
    public function test_it_builds_the_right_query(): void
    {
        $subject = new QueryString('test');

        $expected = [
            'query_string' => [
                'query' => 'test',
                'boost' => 1.0,
            ]
        ];

        $query = $subject->build();

        self::assertSame($expected, $query);
    }
}
