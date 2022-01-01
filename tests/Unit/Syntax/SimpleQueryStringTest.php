<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Syntax;

use JeroenG\Explorer\Domain\Syntax\SimpleQueryString;
use PHPUnit\Framework\TestCase;

class SimpleQueryStringTest extends TestCase
{
    public function test_it_builds_the_right_query(): void
    {
        $subject = new SimpleQueryString('test');

        $expected = [
            'simple_query_string' => [
                'query' => 'test',
                'default_operator' => 'OR',
                'boost' => 1.0,
            ]
        ];

        $query = $subject->build();

        self::assertSame($expected, $query);
    }
}
