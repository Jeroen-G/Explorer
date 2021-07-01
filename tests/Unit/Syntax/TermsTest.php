<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Syntax;

use InvalidArgumentException;
use JeroenG\Explorer\Domain\Syntax\Terms;
use PHPUnit\Framework\TestCase;

class TermsTest extends TestCase
{
    public function test_it_builds_the_right_query(): void
    {
        $subject = new Terms('test', ['value1', 'value2']);

        $expected = ['terms' => ['test' => ['value1', 'value2'], 'boost' => 1.0]];

        $query = $subject->build();

        self::assertSame($expected, $query);
    }

    public function test_it_only_accepts_string_values(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Terms('test', ['value1', null, 2]);
    }
}
