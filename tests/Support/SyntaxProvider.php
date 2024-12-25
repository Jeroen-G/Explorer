<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Support;

use JeroenG\Explorer\Domain\Syntax\Matching;
use JeroenG\Explorer\Domain\Syntax\MultiMatch;
use JeroenG\Explorer\Domain\Syntax\Term;

trait SyntaxProvider
{
    public static function syntaxProvider(): array
    {
        return array_map(fn ($item) => [$item], self::getSyntaxClasses());
    }

    public static function getSyntaxClasses(): array
    {
        return [
            Matching::class,
            Term::class,
            MultiMatch::class
        ];
    }
}
