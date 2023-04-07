<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Support\Models;

use Illuminate\Database\Eloquent\Model;
use JeroenG\Explorer\Application\BePrepared;
use JeroenG\Explorer\Application\Explored;

class TestModelWithPrepare extends Model implements Explored, BePrepared
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

    public function prepare(array $searchable): array
    {
        if ($searchable['data'] === true) {
            $searchable['extra'] = true;
        }
        return $searchable;
    }
}
