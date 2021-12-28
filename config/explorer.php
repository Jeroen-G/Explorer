<?php

declare(strict_types=1);

return [
    /*
     * There are different options for the connection. Since Explorer uses the Elasticsearch PHP SDK
     * under the hood, all the host configuration options of the SDK are applicable here. See
     * https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/configuration.html
     */
    'connection' => [
        'host' => env('ELASTIC_HOST', 'localhost'),
        'port' => env('ELASTIC_PORT', '9200'),
        'scheme' => env('ELASTIC_SCHEME', 'http'),
        // 'user' => env('ELASTIC_USER', ''),
        // 'pass' => env('ELASTIC_PASS', ''),
    ],
    'api' => [
        'cloud-id' => env('ELASTIC_CLOUD_ID', ''),
        'id' => env('ELASTIC_API_ID', ''),
        'key' => env('ELASTIC_API_KEY', ''),
    ],

    /**
     * An index may be defined on an Eloquent model or inline below. A more in depth explanation
     * of the mapping possibilities can be found in the documentation of Explorer's repository.
     */
    'indexes' => [
        // \App\Models\Post::class
    ],
];
