<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\Elastic;

use Elastic\ElasticSearch\Client;
use Elastic\ElasticSearch\ClientBuilder;
use GuzzleHttp\Handler\MockHandler;

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
        $builder = \Elastic\ElasticSearch\ClientBuilder::create();
        $builder->setHosts(['testhost']);
        // $builder->setHandler($handler);
        return new self($builder->build());
    }
}
