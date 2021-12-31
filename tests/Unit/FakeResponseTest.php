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

class FakeResponseTest extends MockeryTestCase
{
    public function test_it_must_get_a_resource_as_body(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FakeResponse(200, ['foo', 'bar']);
    }

    public function test_it_can_build_an_elastic_response(): void
    {
        $file = fopen(__DIR__.'/../Support/fakeresponse.json', 'rb');

        $response = new FakeResponse(200, $file);

        $expected = [
            'status' => 200,
            'transfer_stats' => ['total_time' => 100],
            'body' => $file,
            'effective_url' => 'localhost'
        ];

        self::assertSame($expected, $response->toArray());
    }
}
