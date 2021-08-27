<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit;

use Elasticsearch\Client;
use InvalidArgumentException;
use JeroenG\Explorer\Application\AggregationResult;
use JeroenG\Explorer\Application\SearchCommand;
use JeroenG\Explorer\Domain\Aggregations\TermsAggregation;
use JeroenG\Explorer\Domain\Query\Query;
use JeroenG\Explorer\Domain\Syntax\Compound\BoolQuery;
use JeroenG\Explorer\Domain\Syntax\Matching;
use JeroenG\Explorer\Domain\Syntax\Sort;
use JeroenG\Explorer\Domain\Syntax\Term;
use JeroenG\Explorer\Infrastructure\Elastic\ElasticClientFactory;
use JeroenG\Explorer\Infrastructure\Elastic\FakeResponse;
use JeroenG\Explorer\Infrastructure\Elastic\Finder;
use JeroenG\Explorer\Infrastructure\Scout\ScoutSearchCommandBuilder;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class ElasticClientFactoryTest extends MockeryTestCase
{
    public function test_it_can_construct_a_client(): void
    {
        $client = Mockery::mock(Client::class);
        $factory = new ElasticClientFactory($client);

        self::assertInstanceOf(Mockery\MockInterface::class, $factory->client());
        self::assertEquals($client, $factory->client());
    }

    public function test_it_can_create_a_real_client_with_fake_response(): void
    {
        $file = fopen(__DIR__.'/../Support/fakeresponse.json', 'rb');
        $factory = ElasticClientFactory::fake(new FakeResponse(200, $file));

        self::assertEquals('testhost', $factory->client()->transport->getConnection()->getHost());
        self::assertNotInstanceOf(Mockery\MockInterface::class, $factory->client());
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
