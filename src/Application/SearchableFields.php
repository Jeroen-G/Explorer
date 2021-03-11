<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Application;

interface SearchableFields
{
    public function getSearchableFields(): array;
}
