<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\QueryBuilders;

use JeroenG\Explorer\Domain\Syntax\MultiMatch;
use PHPUnit\Framework\TestCase;

class MultiMatchTest extends TestCase
{
    public function test_it_builds_the_right_query(): void
    {
        $subject = new MultiMatch('test');

        $expected = ['multi_match' => ['query' => 'test']];

        $query = $subject->build();

        self::assertSame($expected, $query);
    }
}
