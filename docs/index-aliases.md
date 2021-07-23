# Index aliases
Aliases allow you to use an index under a different name.
This very useful for zero downtime deployments: first a new index in created and filled, then the alias switches from the old to the new index and finally the old index is deleted.

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
