# Mapping properties in Elasticsearch
The power of Elasticsearch lies in its view of data as a document that can efficiently be searched through in its entirety.
For this, Elasticsearch maps the given parts of the document, such as a Post's ID and title, to types it can work with.
For example, the ID can be mapped to `keyword`, the created_at to `date` and the title to `text`.
If you feed Elasticsearch raw data without a mapping, it will try to infer the types by itself.
A complete overview of all possible types can be found [here](https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping-types.html).
I recommend that you read the [documentation on arrays](https://www.elastic.co/guide/en/elasticsearch/reference/current/array.html) in Elasticsearch in particular.

## Using the configuration
As you may already have read, you may define the mapping by publishing the Explorer configuration and editing config/explorer.php

```bash
php artisan vendor:publish --tag=explorer.config
```

Each index must have a unique name. You do not have to set the type of every field in your model (or [searchable data](https://laravel.com/docs/scout#configuring-searchable-data)),
any other types will be inferred by Elasticsearch.

```php
return [
    'indexes' => [
        'posts' => [
            'properties' => [
                'id' => 'keyword',
                'title' => 'text',
                'created_at' => 'date',
                'published' => 'boolean',
                'author' => 'nested', // TODO[explorer]: not yet supported
            ],
        ],
        'subscribers' => [
            'properties' => [
                'id' => 'keyword',
                'firstname' => 'text',
                'email' => 'text',
                'subscribed_at' => 'date',
            ],
        ],
    ],
];
```

## Using the model

Below is an example of a Post model generated with `php artisan make:model Post` in Laravel 8 and modified for Laravel Scout
with the Searchable trait and the Explored interface for Explorer's mapping using the model.
If you use the configuration method described above you will still need the trait, but not the interface.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JeroenG\Explorer\Explored;
use Laravel\Scout\Searchable;

class Post extends Model implements Explored
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['title', 'published'];

    public function mappableAs(): array
    {
        return [
            'id' => 'keyword',
            'title' => 'text',
            'published' => 'boolean',
            'created_at' => 'date',
        ];
    }
}
```

As the interface states, you now need to write a `mappableAs()` function that will give the (partial) mapping for the index where posts will end up.
The name of the index in this case is inferred from the `searchableAs()` function provided by the Searchable trait. Overwrite this method if you want a different index name.

The last thing that is necessary is tell Explorer that you want this model to be indexed.
Publish the configuration file and add the model to the list of indexes.

```php
return [
    'indexes' => [
        \App\Models\Post::class
    ],
];
```

Perhaps interesting, you may combine the two mapping methods:

```php
return [
    'indexes' => [
        \App\Models\Post::class,
        'subscribers' => [
            'properties' => [
                'id' => 'keyword',
                'firstname' => 'text',
                'email' => 'text',
                'subscribed_at' => 'date',
            ],
        ],
    ],
];
```
