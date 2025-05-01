<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Support\Models;

use Illuminate\Database\Eloquent\Model;
use JeroenG\Explorer\Application\Explored;

class TestModelMissingIndexSettings extends Model implements Explored
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
            'data' => [ 'type' => 'boolean' ],
            'name' => [
                'type' => 'text',
                'analyzer' => 'synonym',
            ]
        ];
    }
}
