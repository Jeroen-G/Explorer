<?php

declare(strict_types=1);

return [
    /**
     * This now follows the same updated configuration options as the official Elasticsearch PHP SDK.
     * https://www.elastic.co/guide/en/elasticsearch/client/php-api/8.13/node_pool.html#config-hash
     */
    'connection' => [
        'ElasticCloudId' => 'YOUR_ELASTIC_CLOUD_ID',
        'ApiKey' => [
            'apiKey' => 'YOUR_API_KEY',
            'id' => 'YOUR_API_KEY_ID',
        ],
    ],

    /**
     * The default index settings used when creating a new index. You can override these settings
     * on a per-index basis by implementing the IndexSettings interface on your model or defining
     * them in the index configuration below.
     */
    'default_index_settings' => [
        //'index' => [],
        //'analysis' => [],
    ],

    /**
     * An index may be defined on an Eloquent model or inline below. A more in depth explanation
     * of the mapping possibilities can be found in the documentation of Explorer's repository.
     */
    'indexes' => [
        // \App\Models\Post::class
    ],

    /**
     * You may opt to keep the old indices after the alias is pointed to a new index.
     * A model is only using index aliases if it implements the Aliased interface.
     */
    'prune_old_aliases' => true,

    /**
     * When set to true, sends all the logs (requests, responses, etc.) from the Elasticsearch PHP SDK
     * to a PSR-3 logger. Disabled by default for performance.
     */
    'logging' => env('EXPLORER_ELASTIC_LOGGER_ENABLED', false),
    'logger' => null,
];
