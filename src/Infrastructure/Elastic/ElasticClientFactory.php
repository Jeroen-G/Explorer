<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\Elastic;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use GuzzleHttp\Client as GuzzleClient;
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
        $handler = new MockHandler([$response->toResponse()]);

        $builder = ClientBuilder::create();
        $builder->setHosts(['testhost']);
        $builder->setHttpClient(new GuzzleClient(['handler' => $handler]));
        return new self($builder->build());
    }
}
