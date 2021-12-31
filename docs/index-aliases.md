# Index aliases
Aliases allow you to use an index under a different name.This very useful for zero downtime deployments.

There are three aliases created: a read alias, a write alias and a history alias.
The read alias is used for reading, the write index is used for writing.
The history aggregates all old indices, which can be pruned.
When updating an index it is recreated to a new index with a unique name.
The "write" alias is pointed to the new index and all Scout updates will be forwarded to the "write" index.
After all entities are imported the "read" alias will also be pointed to the new index.

If you wish to keep the old indices set `prune_old_aliases` to false in `config/explorer.php`

## Using aliases
A model is only using index aliases if it implements the Aliased interface or enabled in the configuration (see [mapping](mapping.md)).

After that, any time you use the `scout:import` command a new index will be created and when the insertion of models is done the alias will be pointed to the new index.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JeroenG\Explorer\Application\Explored;
use JeroenG\Explorer\Application\Aliased;
use Laravel\Scout\Searchable;

class Post extends Model implements Explored, Aliased
{
    use HasFactory;
    use Searchable;

    //...
}
```

```php
return [
    'indexes' => [
        'posts' => [
            'aliased' => true,
            'properties' => [
                'id' => 'keyword',
                'title' => 'text',
                'created_at' => 'date',
                'published' => 'boolean',
                'author' => 'nested',
            ],
        ],
    ],
];
```
