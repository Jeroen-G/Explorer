<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Syntax;

use JeroenG\Explorer\Domain\Syntax\Wildcard;
use PHPUnit\Framework\TestCase;

class WildcardTest extends TestCase
{
    public function test_it_builds_wildcard(): void
    {
        $field = ':field:';
        $value = ':value:';
        $boost = 42.0;
        $caseInsensitive = false;
        $rewrite = null;

        $wildcard = new Wildcard($field, $value, $boost, $caseInsensitive, $rewrite);

        self::assertEquals([
            'wildcard' => [ $field => [ 'value' => $value, 'boost' => $boost, 'case_insensitive' => $caseInsensitive, 'rewrite' => $rewrite ] ],
        ], $wildcard->build());
    }

    public function test_it_builds_with_defaults(): void
    {
        $field = ':field:';
        $value = ':value:';

        $wildcard = new Wildcard($field, $value);

        self::assertEquals([
            'wildcard' => [ $field => [ 'value' => $value, 'boost' => 1.0, 'case_insensitive' => false, 'rewrite' => null ] ],
        ], $wildcard->build());
    }
}
