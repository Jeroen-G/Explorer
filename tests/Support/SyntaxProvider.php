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
        return [
            'matching'   => [Matching::class, ['testcase']],
            'term'       => [Term::class, ['testcase', ':val:']],
            'multimatch' => [MultiMatch::class, ['testcase']],
        ];
    }
}
