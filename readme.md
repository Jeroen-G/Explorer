# Explorer

[![Latest Version on Packagist][ico-version]][link-packagist]

[![CI](https://github.com/Jeroen-G/Explorer/actions/workflows/ci.yml/badge.svg)](https://github.com/Jeroen-G/Explorer/actions/workflows/ci.yml)

Next-gen Elasticsearch driver for Laravel Scout with the power of Elasticsearch's queries.

## Installation

Via Composer

``` bash
composer require jeroen-g/explorer
```

You will need the configuration file to define your indexes:

```bash
php artisan vendor:publish --tag=explorer.config
```

Also do not forget to follow the [installation instructions for Laravel Scout](https://laravel.com/docs/scout#installation),
and in your Laravel Scout config, set the driver to `elastic`. 

## Usage

Be sure to also have a look at the [docs](docs/index.md) to see what is possible!
There is also a [demo app](https://github.com/Jeroen-G/explorer-demo) available that might be insightful.

### Configuration

You may either define the mapping for you index in the config file:

```php
return [
    'indexes' => [
        'posts_index' => [
            'properties' => [
                'id' => 'keyword',
                'title' => 'text',
            ],
        ]
    ]
];
```

Or you may define the model for the index, and the rest will be decided for you:

```php
return [
    'indexes' => [
        \App\Models\Post::class
    ],
];
```

In the last case you may implement the `Explored` interface and overwrite the mapping with the `mappableAs()` function.

Essentially this means that it is up to you whether you like having it all together in the model, or separately in the config file.

### Advanced queries
The documentation of Laravel Scout states that "more advanced "where" clauses are not currently supported".
Only a simple check for ID is possible besides the standard fuzzy term search:

```php
$posts = Post::search('lorem ipsum')->get();
```

Explorer expands your possibilities using query builders to write more complex queries.

For example, to get all posts that:

 - are published
 - have "lorem" somewhere in the document
 - have "ipsum" in the title
 - maybe have a tag "featured", if so boost its score by 2
 
 You could execute this search query:

```php
$posts = Post::search('lorem')
    ->must(new Matching('title', 'ipsum'))
    ->should(new Terms('tags', ['featured'], 2))
    ->filter(new Term('published', true))
    ->get();
```

### Commands
Be sure you have configured your indexes first in `config/explorer.php` and run the Scout commands.

#### Searching indexes

```bash
php artisan elastic:search "App\Models\Post" lorem
```

## Changelog

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Credits

- [Jeroen][link-author-jeroen]
- [Vincent][link-author-vincent]
- [All Contributors][link-contributors]

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/jeroen-g/explorer.svg?style=flat-square
[ico-actions]: https://img.shields.io/github/workflow/status/Jeroen-G/explorer/CI?label=CI%2FCD&style=flat-square

[link-actions]: https://github.com/Jeroen-G/explorer/actions?query=workflow:CI
[link-packagist]: https://packagist.org/packages/jeroen-g/explorer
[link-author-jeroen]: https://github.com/jeroen-g
[link-author-vincent]: https://github.com/blackshadev
[link-contributors]: ../../contributors
