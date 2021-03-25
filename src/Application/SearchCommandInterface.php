<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Application;

interface SearchCommandInterface
{
    public function getIndex(): ?string;

    public function buildQuery(): array;
}
