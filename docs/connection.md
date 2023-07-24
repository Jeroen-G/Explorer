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

## Elastic Cloud ID

Another connection option is to use an elastic cloud id as shown below
```php
    return [
        'connection' => [
            'elasticCloudId' => 'staging:dXMtZWFzdC0xLmF3cy5mb3VuZC5pbyRjZWM2ZjI2MWE3NGJmMjRjZTMzYmI4ODExYjg0Mjk0ZiRjNmMyY2E2ZDA0MjI0OWFmMGNjN2Q3YTllOTYyNTc0Mw',
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

## Verify TLS with CA

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

## TLS connection with a public certificate and private key
```
    return [
        'connection' => [
            'host' => 'localhost',
            'port' => '9200',
            'scheme' => 'https',
            'ssl' => [
                'cert' => ['path/to/cert.pem', 'passphrase'],
                'key' => ['path/to/key.pem', 'passphrase'],
            ],
        ],
    ];
```

## Multiple connections

Elastic can also have multiple possible connections

```php
    use Elasticsearch\ConnectionPool\Selectors\RoundRobinSelector;
    
    return [
        'connection' => [
            'host' => 'localhost',
            'port' => '9200',
            'scheme' => 'http',
            'ssl' => [
                'verify' => false,
            ],
            'selector' => RoundRobinSelector::class
        ],
        'additionalConnections' => [
            [
                'host' => 'localhost',
                'port' => '9201',
                'scheme' => 'http',
            ],
            [
                'host' => 'localhost',
                'port' => '9202',
                'scheme' => 'http',
            ]
        ],
    ];
```
