<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Query\QueryProperties;

interface Combinable
{
    /**
     * @param static ...$self
     */
    public function combine(...$self): QueryProperty;
}