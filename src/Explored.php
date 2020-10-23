<?php

namespace JeroenG\Explorer;

interface Explored
{
    public function getScoutKey();

    public function searchableAs();

    public function toSearchableArray();

    public function mappableAs(): array;
}
