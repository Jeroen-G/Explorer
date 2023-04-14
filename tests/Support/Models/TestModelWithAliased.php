<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Support\Models;

use Illuminate\Database\Eloquent\Model;
use JeroenG\Explorer\Application\Aliased;
use JeroenG\Explorer\Application\Explored;

class TestModelWithAliased extends Model implements Explored, Aliased
{
    public function getScoutKey(): string
    {
        return ':scout_key:';
    }

    public function searchableAs(): string
    {
        return ':searchable_as:';
    }

    public function toSearchableArray(): array
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
