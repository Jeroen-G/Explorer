<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Syntax;

use JeroenG\Explorer\Domain\Syntax\Nested;
use JeroenG\Explorer\Domain\Syntax\Term;
use PHPUnit\Framework\TestCase;

class NestedTest extends TestCase
{
    public function test_it_builds_the_right_query(): void
    {
        $subject = new Nested('test', new Term('test.id', '5', 5.5));

        $expected = [
            'nested' => [
                'path' => 'test',
                'query' => [
                    'term' => [
                        'test.id' => '5',
                        'boost' => 5.5
                    ]
                ]
            ]
        ];

        $query = $subject->build();

        self::assertSame($expected, $query);
    }
}
