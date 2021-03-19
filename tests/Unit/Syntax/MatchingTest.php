<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Syntax;

use JeroenG\Explorer\Domain\Syntax\Matching;
use PHPUnit\Framework\TestCase;

class MatchingTest extends TestCase
{
    public function test_it_builds_the_right_query(): void
    {
        $expectation = ['match' => ['test' => ['query' => 'value', 'fuzziness' => 'auto']]];
        $subject = new Matching('test', 'value');

        $result = $subject->build();

        self::assertEquals($expectation, $result);
    }

    public function test_it_builds_with_fuzziness(): void
    {
        $expectation = ['match' => ['test' => ['query' => 'value', 'fuzziness' => 2]]];
        $subject = new Matching('test', 'value', 2);

        $result = $subject->build();

        self::assertEquals($expectation, $result);
    }

    public function test_it_builds_without_fuzziness(): void
    {
        $expectation = ['match' => ['test' => ['query' => 'value']]];
        $subject = new Matching('test', 'value', null);

        $result = $subject->build();

        self::assertEquals($expectation, $result);
    }
}
