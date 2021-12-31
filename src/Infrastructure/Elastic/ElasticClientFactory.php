<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\Elastic;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use GuzzleHttp\Ring\Client\MockHandler;

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

    public static function fake(FakeResponse $response): ElasticClientFactory
    {
        $handler = new MockHandler($response->toArray());
        $builder = ClientBuilder::create();
        $builder->setHosts(['testhost']);
        $builder->setHandler($handler);
        return new self($builder->build());
    }
}
