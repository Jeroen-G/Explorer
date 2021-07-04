<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit;

use Elasticsearch\Client;
use JeroenG\Explorer\Infrastructure\Elastic\ElasticClientFactory;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class ElasticClientFactoryTest extends MockeryTestCase
{
    public function test_it_can_construct_a_client(): void
    {
        $client = Mockery::mock(Client::class);
        $factory = new ElasticClientFactory($client);

        self::assertEquals($client, $factory->client());
    }
}
