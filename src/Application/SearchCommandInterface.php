<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Application;

use JeroenG\Explorer\Domain\Query\Query;

interface SearchCommandInterface
{
    public function getIndex(): ?string;

    public function buildQuery(): array;
}
