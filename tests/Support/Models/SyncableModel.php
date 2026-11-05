<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Support\Models;

use Illuminate\Database\Eloquent\Model;

class SyncableModel extends Model
{
    public function syncWithSearchUsingQueue(): string
    {
        return ':queue:';
    }

    public function syncWithSearchUsing(): string
    {
        return ':connection:';
    }
}
