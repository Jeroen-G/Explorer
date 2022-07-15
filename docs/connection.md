# Connection

Explorer connect to ElasticSearch through the PHP ElasticSearch client and has several options to configure a connection.
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
                'username' => 'piet',
                'password' => 'henk'
            ],
        ],
    ];
```

## Verify SSL with CA

From Elastic 8 and upward TLS is becoming the default, even on develop. This means you'd need to verify the ca. You can set the `ssl.verify` config key to the path of the ca, or to `false` to disable verification al together.
Warning: Disabling ca verification on production is not recommended. 

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

