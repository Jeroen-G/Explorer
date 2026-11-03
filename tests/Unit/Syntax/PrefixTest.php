<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Syntax;

use JeroenG\Explorer\Domain\Syntax\Prefix;
use PHPUnit\Framework\TestCase;

class PrefixTest extends TestCase
{
    public function test_it_builds_a_prefix_query(): void
    {
        $prefix = new Prefix('user', 'ki', 2.0, true, 'scoring_boolean');

        self::assertSame([
            'prefix' => [
                'user' => [
                    'value' => 'ki',
                    'boost' => 2.0,
                    'case_insensitive' => true,
                    'rewrite' => 'scoring_boolean',
                ],
            ],
        ], $prefix->build());
    }

    public function test_it_builds_with_defaults(): void
    {
        $prefix = new Prefix('user', 'ki');

        self::assertSame([
            'prefix' => [
                'user' => [
                    'value' => 'ki',
                    'boost' => 1.0,
                    'case_insensitive' => false,
                    'rewrite' => 'constant_score',
                ],
            ],
        ], $prefix->build());
    }
}
