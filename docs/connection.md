# Connection

Explorer connects to ElasticSearch through the PHP ElasticSearch client and has several options to configure a connection.
The connection configuration is defined `config/explorer.php`. 

See * https://www.elastic.co/guide/en/elasticsearch/client/php-api/8.13/node_pool.html#config-hash

## Basic

The most basic connection is with http without authorization.
```php
    return [
        'connection' => [
            'Hosts' => ['localhost:9200'],
        ],
    ];
```

## Elastic Cloud ID

Another connection option is to use an elastic cloud id as shown below
```php
    return [
        'connection' => [
            'ElasticCloudId' => 'staging:dXMtZWFzdC03LmF3cy5mb3VuZC5pbyRjZWM2ZjI2MWE3SGJmMjRjZvMzYmI4ODExYjg0Mjk0ZiRjNmMyY2E2ZDA0MjI0OWFmMGNjN2Q3YTllOTYyNTc0Mw',
        ],
    ];
```

## * *Preferred Method* -  Encoded API key and id

Replace the auth part with API and give it your encoded id and key.

```php
    return [
        'connection' => [
            'ElasticCloudId' => 'staging:dXMtZWFzdC03LmF3cy5mb3VuZC5pbyRjZWM2ZjI2MWE3SGJmMjRjZvMzYmI4ODExYjg0Mjk0ZiRjNmMyY2E2ZDA0MjI0OWFmMGNjN2Q3YTllOTYyNTc0Mw',
            'ApiKey' => [
                'apiKey' => 'myEncodedKeyAndID'
            ],
        ],
    ];
```

## Basic Authorization

To specify a username and password use the `auth` key with a `username` and a `password`.

```php
    return [
        'connection' => [
            'Hosts' => ['localhost:9200'],
            'BasicAuthentication' => [
                'username' => 'myName',
                'password' => 'myPassword'
            ],
        ],
    ];
```

## API key and id

```php
    return [
        'connection' => [
            'Hosts' => ['localhost:9200'],
            'ApiKey' => [
                'id' => 'myId',
                'apiKey' => 'myKey'
            ],
        ],
    ];

## Verify TLS with CA

From Elastic 8 and upwards TLS is becoming the default, even in development. This means you will need to verify the CA. You can set the `ssl.verify` config key to the path of the CA, or to `false` to disable verification altogether.

> **Warning**
> Disabling CA verification on production is not recommended.

```php
    return [
        'connection' => [
            'Hosts' => ['localhost:9200'],
            'CABundle' => './path/to/ca.crt',
        ],
    ];
```

To disable TLS verification set it to `false`. **NOT recommended for production**.
```php
    return [
        'connection' => [
            'Hosts' => ['localhost:9200'],
            'SSLVerification' => false,
        ],
    ];
```

## TLS connection with a public certificate and private key
```
    return [
        'connection' => [
            'Hosts' => ['localhost:9200'],
            'SSLCert' => [
                'cert' => './path/to/cert.crt',
                'password' => null,
            ],
            'SSLKey' => [
                'key' => './path/to/key.key',
                password' => null,
            ],
        ],
    ];
```

## Multiple connections

Elastic can also have multiple possible connections

```php

    return [
        'connection' => [
            'Hosts' => ['host1:9200', 'host2:9200'],
            'SSLVerification' => true,
        ],
    ];
```
