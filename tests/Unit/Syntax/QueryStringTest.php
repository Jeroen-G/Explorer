<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Syntax;

use InvalidArgumentException;
use JeroenG\Explorer\Domain\Syntax\QueryString;
use PHPUnit\Framework\TestCase;

class QueryStringTest extends TestCase
{
    public function test_it_builds_the_right_query(): void
    {
        $subject = new QueryString('test');

        $expected = [
            'query_string' => [
                'query' => 'test',
                'default_operator' => 'OR',
                'boost' => 1.0,
            ]
        ];

        $query = $subject->build();

        self::assertSame($expected, $query);
    }

    public function test_it_can_accept_a_custom_default_operator(): void
    {
        $subject = new QueryString('test', QueryString::OP_AND);

        $expected = [
            'query_string' => [
                'query' => 'test',
                'default_operator' => 'AND',
                'boost' => 1.0,
            ]
        ];

        $query = $subject->build();

        self::assertSame($expected, $query);
    }

    public function test_it_only_allows_the_operators_or_and(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new QueryString('test', 'X');
    }
}
