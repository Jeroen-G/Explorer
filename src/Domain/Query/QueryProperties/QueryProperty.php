<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Query\QueryProperties;

interface QueryProperty
{
    public function build(): array;
}