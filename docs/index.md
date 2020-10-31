# Explorer

[![Latest Version on Packagist][ico-version]][link-packagist]
[![CI][ico-actions]][link-actions]

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

Also do not forget to follow the [installation instructions for Laravel Scout](https://laravel.com/docs/scout#installation) and set the driver to `elastic`. 

# Explorer documentation

- [Quickstart](quickstart.md)
- [Mapping properties in Elasticsearch](mapping.md)
- [Advanced queries](advanced-queries.md)
- [Sorting search results](sorting.md)

[ico-version]: https://img.shields.io/packagist/v/jeroen-g/explorer.svg?style=flat-square
[ico-actions]: https://img.shields.io/github/workflow/status/Jeroen-G/explorer/CI?label=CI%2FCD&style=flat-square
[link-actions]: https://github.com/Jeroen-G/alpine-artisan/actions?query=workflow%3ACI%2FCD
[link-packagist]: https://packagist.org/packages/jeroen-g/explorer
