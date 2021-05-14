<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Syntax;

use JeroenG\Explorer\Domain\Syntax\Invert;
use JeroenG\Explorer\Domain\Syntax\MatchAll;
use PHPUnit\Framework\TestCase;

class InvertTest extends TestCase
{
    public function test_it_builds_invert(): void
    {
        $invert = Invert::query(new MatchAll());

        self::assertEquals(['bool' => [ 'must_not' => [ 'match_all' => (object)[] ] ]], $invert->build());
    }
}
