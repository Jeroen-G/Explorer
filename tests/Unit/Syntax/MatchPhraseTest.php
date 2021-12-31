<?php

declare(strict_types = 1);

namespace JeroenG\Explorer\Tests\Unit\Syntax;

use JeroenG\Explorer\Domain\Syntax\MatchPhrase;
use PHPUnit\Framework\TestCase;

class MatchPhraseTest extends TestCase
{
    public function test_it_builds_the_right_query(): void
    {
        $expectation = ['match_phrase' => ['test' => ['query' => 'lorem ipsum dolor']]];
        $subject = new MatchPhrase('test', 'lorem ipsum dolor');

        $result = $subject->build();

        self::assertEquals($expectation, $result);
    }
}
