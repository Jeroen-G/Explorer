<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Support\Models;

use JeroenG\Explorer\Application\Explored;

class TestModelWithoutSettings implements Explored
{
    public function getScoutKey()
    {
        return ':scout_key:';
    }

    public function searchableAs()
    {
        return ':searchable_as:';
    }

    public function toSearchableArray()
    {
        return [ 'data' => true ];
    }

    public function mappableAs(): array
    {
        return [
            'data' => [ 'type' => 'boolean' ]
        ];
    }
}
