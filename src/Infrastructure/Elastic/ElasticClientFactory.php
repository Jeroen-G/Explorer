<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\Elastic;

use Elasticsearch\Client;

final class ElasticClientFactory
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function client(): Client
    {
        return $this->client;
    }
}
