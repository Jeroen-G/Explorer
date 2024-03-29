<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Syntax;

use JeroenG\Explorer\Domain\Syntax\MultiMatch;
use PHPUnit\Framework\TestCase;

class MultiMatchTest extends TestCase
{
    public function test_it_builds_the_right_query(): void
    {
        $subject = new MultiMatch('test');

        $expected = ['multi_match' => ['query' => 'test', 'fuzziness' => 'auto']];

        $query = $subject->build();

        self::assertEquals($expected, $query);
    }

    public function test_it_builds_with_fields(): void
    {
        $subject = new MultiMatch('test', ['test1', 'test2']);

        $expected = ['multi_match' => ['query' => 'test', 'fuzziness' => 'auto', 'fields' => ['test1', 'test2']]];

        $query = $subject->build();

        self::assertEquals($expected, $query);
    }

    public function test_it_builds_with_empty_fields(): void
    {
        $subject = new MultiMatch('test', []);

        $expected = ['multi_match' => ['query' => 'test', 'fuzziness' => 'auto', 'fields' => []]];

        $query = $subject->build();

        self::assertEquals($expected, $query);
    }

    public function test_it_builds_with_fuzziness(): void
    {
        $subject = new MultiMatch('test', null, 2);

        $expected = ['multi_match' => ['query' => 'test', 'fuzziness' => 2]];

        $query = $subject->build();

        self::assertEquals($expected, $query);
    }

    public function test_it_builds_with_prefix_lenght(): void
    {
        $subject = new MultiMatch('test', null, null, 2);

        $expected = ['multi_match' => ['query' => 'test', 'prefix_length' => 2]];

        $query = $subject->build();

        self::assertEquals($expected, $query);
    }
}
