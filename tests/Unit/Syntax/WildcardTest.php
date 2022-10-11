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

        $wildcard = new Wildcard($field, $value, $boost);

        self::assertEquals([
            'wildcard' => [ $field => [ 'value' => $value, 'boost' => $boost, 'case_insensitive' => false ] ],
        ], $wildcard->build());
    }

    public function test_it_builds_with_defaults(): void
    {
        $field = ':field:';
        $value = ':value:';

        $wildcard = new Wildcard($field, $value);

        self::assertEquals([
            'wildcard' => [ $field => [ 'value' => $value, 'boost' => 1.0, 'case_insensitive' => false ] ],
        ], $wildcard->build());
    }

    public function test_it_builds_with_case_insensitive(): void
    {
        $field = ':field:';
        $value = ':value:';

        $wildcard = new Wildcard($field, $value);

        $caseInsensitive = true;
        $wildcard->setCaseInsensitive($caseInsensitive);

        self::assertEquals([
            'wildcard' => [ $field => [ 'value' => $value, 'boost' => 1.0, 'case_insensitive' => $caseInsensitive ] ],
        ], $wildcard->build());
    }

    public function test_it_builds_with_rewrite(): void
    {
        $field = ':field:';
        $value = ':value:';

        $wildcard = new Wildcard($field, $value);

        $rewrite = 'constant_score';
        $wildcard->setRewrite($rewrite);

        self::assertEquals([
            'wildcard' => [ $field => [ 'value' => $value, 'boost' => 1.0, 'case_insensitive' => false, 'rewrite' => $rewrite ] ],
        ], $wildcard->build());
    }
}
