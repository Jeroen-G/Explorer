<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit;

use Elastic\Elasticsearch\ClientInterface;
use JeroenG\Explorer\Application\SearchCommand;
use JeroenG\Explorer\Domain\Query\Query;
use JeroenG\Explorer\Domain\Syntax\Compound\BoolQuery;
use JeroenG\Explorer\Infrastructure\Elastic\ElasticClientFactory;
use JeroenG\Explorer\Infrastructure\Elastic\FakeResponse;
use JeroenG\Explorer\Infrastructure\Elastic\Finder;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class ElasticClientFactoryTest extends MockeryTestCase
{
    public function test_it_can_construct_a_client(): void
    {
        $client = ClientExpectation::create();
        $factory = new ElasticClientFactory($client->getMock());

        self::assertInstanceOf(Mockery\MockInterface::class, $factory->client());
        self::assertEquals($client->getMock(), $factory->client());
    }

    public function test_it_can_make_a_faked_call(): void
    {
        $file = fopen(__DIR__.'/../Support/fakeresponse.json', 'rb');
        $factory = ElasticClientFactory::fake(new FakeResponse(200, $file));

        $finder = new Finder($factory->client(), new SearchCommand('test', Query::with(new BoolQuery())));
        $results = $finder->find();

        self::assertEquals(2, $results->count());
    }
}
