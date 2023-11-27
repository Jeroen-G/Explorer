# Logging

When enabled the Elastic SDK will send all its logs (such as requests and responses) to a PSR-3 logger. 
By default the logger is disabled for performance.
To enable the logger set `EXPLORER_ELASTIC_LOGGER_ENABLED=true` in your environment variables and edit your `explorer.php` config to define a logger (see below for examples).

See also the [SDK](https://github.com/elastic/elasticsearch-php/blob/main/docs/logger.asciidoc) docs.
More information on loggers in Laravel can be found in [Laravel's docs](https://laravel.com/docs/logging).

If you pass a string value as the logger it will be interpreted as the name of a log channel (see Laravel's docs for more information).

Examples:
```php
'logger' => new \Psr\Log\NullLogger(),
```

```php
'logger' => \Illuminate\Support\Facades\Log::channel('daily'),
```

```php
'logger' => 'daily',
```
