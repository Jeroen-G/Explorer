<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Support;

use JeroenG\Explorer\Domain\Compound\QueryType;

trait QueryTypeProvider
{
    public function getQueryTypes(): array
    {
        return QueryType::ALL;
    }

    public function queryTypeProvider(): array
    {
        return array_map(fn ($item) => [$item], $this->getQueryTypes());
    }
}
