<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Syntax;

use JeroenG\Explorer\Domain\Syntax\DisjunctionMax;
use JeroenG\Explorer\Domain\Syntax\MatchAll;
use PHPUnit\Framework\TestCase;

class DisjunctionMaxTest extends TestCase
{
    public function test_it_builds_empty_disjunction_max(): void
    {
        $dismax = DisjunctionMax::queries([]);

        self::assertEquals(['dis_max' => [ 'queries' => [] ]], $dismax->build());
    }

    public function test_it_builds_disjunction_max(): void
    {
        $dismax = DisjunctionMax::queries([
            new MatchAll(),
            new MatchAll(),
        ]);

        self::assertEquals(['dis_max' => [ 'queries' => [
            [ 'match_all' => (object)[]],
            [ 'match_all' => (object)[]]
        ] ]], $dismax->build());
    }
}
