<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax\Compound;

interface QueryType
{
    public const MUST = 'must';

    public const SHOULD = 'should';

    public const FILTER = 'filter';

    public const ALL = [self::MUST, self::SHOULD, self::FILTER];
}
