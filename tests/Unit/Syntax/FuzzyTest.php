<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Syntax;

use JeroenG\Explorer\Domain\Syntax\Fuzzy;
use PHPUnit\Framework\TestCase;

class FuzzyTest extends TestCase
{
    public function test_it_builds_a_fuzzy_query(): void
    {
        $fuzzy = new Fuzzy('user', 'ki', 2, 25, 1, false, 'scoring_boolean', 2.0);

        self::assertSame([
            'fuzzy' => [
                'user' => [
                    'value' => 'ki',
                    'fuzziness' => 2,
                    'max_expansions' => 25,
                    'prefix_length' => 1,
                    'transpositions' => false,
                    'rewrite' => 'scoring_boolean',
                    'boost' => 2.0,
                ],
            ],
        ], $fuzzy->build());
    }

    public function test_it_builds_with_defaults(): void
    {
        $fuzzy = new Fuzzy('user', 'ki');

        self::assertSame([
            'fuzzy' => [
                'user' => [
                    'value' => 'ki',
                    'fuzziness' => 'AUTO',
                    'max_expansions' => 50,
                    'prefix_length' => 0,
                    'transpositions' => true,
                    'rewrite' => 'constant_score',
                    'boost' => 1.0,
                ],
            ],
        ], $fuzzy->build());
    }
}
