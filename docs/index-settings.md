# Index settings
Most of the configuration you will be doing through the [mapping](mapping.md) of your index.
However, if for example you want to define more advanced Elasticsearch settings such as [analyzers](https://www.elastic.co/guide/en/elasticsearch/reference/current/analyzer.html) or [tokenizers](https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-tokenizers.html) you need to do so using index settings.

Be aware that any time you change the index settings, you need to [recreate](commands.md) the index.

## Using models
To start using index settings, we will expand on the Post model with an `indexSettings` function to set an analyzer.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JeroenG\Explorer\Application\Explored;
use JeroenG\Explorer\Application\IndexSettings;use Laravel\Scout\Searchable;

class Post extends Model implements Explored, IndexSettings
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
    
    public function indexSettings(): array
    {
        return [
            'analysis' => [
                'analyzer' => [
                    'standard_lowercase' => [
                        'type' => 'custom',
                        'tokenizer' => 'standard',
                        'filter' => ['lowercase'],
                    ],
                ],
            ],
        ];
    }
}
```

If you want to create an analyzer object-oriented, [continue reading here](text-analysis.md).

## Using configuration arrays
If you want to use the configuration array notation (see [mapping](mapping.md)), you may add the settings as follows:

```php
return [
    'indexes' => [
        'posts' => [
            'settings' => [
                'analysis' => [
                    'analyzer' => [
                        'standard_lowercase' => [
                            'type' => 'custom',
                            'tokenizer' => 'standard',
                            'filter' => ['lowercase'],
                        ],
                    ],
                ],
            ],
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

## Default index settings
If you want to set default index settings for all your indices, you can do so by adding the `default_index_settings` key to your configuration file:

```php
return [
    'default_index_settings' => [
        'index' => [
            'max_result_window' => 100000,
        ],
        'analysis' => [
            'analyzer' => [
                'standard_lowercase' => [
                    'type' => 'custom',
                    'tokenizer' => 'standard',
                    'filter' => ['lowercase'],
                ],
            ],
        ],
    ],
];
```