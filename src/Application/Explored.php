<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Application;

interface Explored
{
    public function getScoutKey();

    public function searchableAs();

    public function toSearchableArray();

    public function mappableAs(): array;
}
