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

Also do not forget to follow the [installation instructions for Laravel Scout](https://laravel.com/docs/scout#installation) and set the driver to `elastic`. 

# Explorer documentation

- [Quickstart](quickstart.md)
- [Connection](connection.md)
- [Mapping properties in Elasticsearch](mapping.md)
- [Sorting search results](sorting.md)
- [Pagination and search result size](pagination.md)
- [Debugging](debugging.md)
- [Logging](logging.md)
- [Testing](testing.md)
- [Console commands](commands.md)
- [Text analysis](text-analysis.md)
- [Preparing data](preparing-data.md)
- [Advanced queries](advanced-queries.md)
- [Advanced index settings](index-settings.md)
- [Index aliases](index-aliases.md)
- [Aggregations](aggregations.md)

[ico-version]: https://img.shields.io/packagist/v/jeroen-g/explorer.svg?style=flat-square
[ico-actions]: https://img.shields.io/github/workflow/status/Jeroen-G/explorer/CI?label=CI%2FCD&style=flat-square
[link-actions]: https://github.com/Jeroen-G/alpine-artisan/actions?query=workflow%3ACI%2FCD
[link-packagist]: https://packagist.org/packages/jeroen-g/explorer
