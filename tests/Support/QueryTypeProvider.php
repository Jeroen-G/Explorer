<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Support;

use JeroenG\Explorer\Domain\Syntax\Compound\QueryType;

trait QueryTypeProvider
{
    public static function getQueryTypes(): array
    {
        return QueryType::ALL;
    }

    public static function queryTypeProvider(): array
    {
        return array_map(fn ($item) => [$item], self::getQueryTypes());
    }
}
