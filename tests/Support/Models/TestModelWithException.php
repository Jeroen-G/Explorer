<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Support\Models;

use Illuminate\Database\Eloquent\Model;
use JeroenG\Explorer\Application\BePrepared;
use JeroenG\Explorer\Application\Explored;

class TestModelWithException extends Model implements Explored, BePrepared
{
    private string $exceptionType;

    public function __construct(string $exceptionType = 'toSearchableArray')
    {
        parent::__construct();
        $this->exceptionType = $exceptionType;
    }

    public function getScoutKey(): string
    {
        return ':scout_key_exception:';
    }

    public function searchableAs(): string
    {
        return ':searchable_as:';
    }

    public function toSearchableArray(): array
    {
        if ($this->exceptionType === 'toSearchableArray') {
            throw new \Exception('Error in toSearchableArray method');
        }
        return ['data' => true];
    }

    public function mappableAs(): array
    {
        return [
            'data' => ['type' => 'boolean']
        ];
    }

    public function prepare(array $searchable): array
    {
        if ($this->exceptionType === 'prepare') {
            throw new \Exception('Error in prepare method');
        }
        return $searchable;
    }
}