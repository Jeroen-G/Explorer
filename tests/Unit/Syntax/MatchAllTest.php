<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Syntax;

use JeroenG\Explorer\Domain\Syntax\MatchAll;
use PHPUnit\Framework\TestCase;

class MatchAllTest extends TestCase
{
    public function test_it_builds_match_all(): void
    {
        $matchAll = new MatchAll();
        self::assertEquals(['match_all' => (object)[]], $matchAll->build());
    }
}
