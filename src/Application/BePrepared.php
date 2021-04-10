<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Application;

interface BePrepared
{
    public function prepare(array $searchable): array;
}
