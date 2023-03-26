<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Syntax;

use InvalidArgumentException;
use JeroenG\Explorer\Domain\Syntax\RegExp;
use JeroenG\Explorer\Domain\Syntax\Terms;
use PHPUnit\Framework\TestCase;

class RegExpTest extends TestCase
{
    public function test_it_builds_the_right_query(): void
    {
        $subject = new RegExp('test', '[aA].+');

        $expected = ['regexp' => ['test' => ['value' => '[aA].+', 'flags' => 'ALL', 'case_insensitive' => false]]];

        $query = $subject->build();

        self::assertSame($expected, $query);
    }
}
