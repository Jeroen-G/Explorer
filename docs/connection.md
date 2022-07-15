# Connection

Explorer connects to ElasticSearch through the PHP ElasticSearch client and has several options to configure a connection.
The connection configuration is defined `config/explorer.php`. 

## Basic

The most basic connection is with http without authorization.
```php
    return [
        'connection' => [
            'host' => 'localhost',
            'port' => '9200',
            'scheme' => 'http',
        ],
    ];
```

## Basic Authorization

To specify a username and password use the `auth` key with a `username` and a `password`.

```php
    return [
        'connection' => [
            'host' => 'localhost',
            'port' => '9200',
            'scheme' => 'http',
            'auth' => [
                'username' => 'myName',
                'password' => 'myPassword'
            ],
        ],
    ];
```

## API key

Replace the auth part with API and give it your key and id.

```php
    return [
        'connection' => [
            'host' => 'localhost',
            'port' => '9200',
            'scheme' => 'http',
            'api' => [
                'id' => 'myId',
                'key' => 'myKey'
            ],
        ],
    ];
```

## Verify SSL with CA

From Elastic 8 and upwards TLS is becoming the default, even in development. This means you will need to verify the CA. You can set the `ssl.verify` config key to the path of the CA, or to `false` to disable verification altogether.

> **Warning**
> Disabling CA verification on production is not recommended.

```php
    return [
        'connection' => [
            'host' => 'localhost',
            'port' => '9200',
            'scheme' => 'http',
            'ssl' => [
                'verify' => './path/to/ca.crt',
            ],
        ],
    ];
```

To disable TLS verification set it to `false`. **NOT recommended for production**.
```php
    return [
        'connection' => [
            'host' => 'localhost',
            'port' => '9200',
            'scheme' => 'http',
            'ssl' => [
                'verify' => false,
            ],
        ],
    ];
```

