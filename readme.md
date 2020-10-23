# Explorer

[![Latest Version on Packagist][ico-version]][link-packagist]

This is where your description should go.

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

### Commands
Be sure you have configured your indexes first in `config/explorer.php`.

#### Creating indexes

```bash
php artisan elastic:create
```

#### Deleting indexes

```bash
php artisan elastic:delete
```

#### Searching indexes

```bash
php artisan elastic:search "App\Models\Post" lorem
```

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Credits

- [Jeroen][link-author]
- [All Contributors][link-contributors]

## License

license. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/jeroen-g/explorer.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/jeroen-g/explorer
[link-author]: https://github.com/jeroen-g
[link-contributors]: ../../contributors
