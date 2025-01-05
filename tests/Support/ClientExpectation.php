<?php
declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Support;

use Elastic\Elasticsearch\Client;
use Mockery;
use Mockery\MockInterface;

final class ClientExpectation
{
    private MockInterface|Client $mock;

    public function __construct(MockInterface|Client $mock)
    {
        $this->mock = $mock;
    }

    public static function create(): self
    {

        return new self(Mockery::mock(Client::class));
    }

    public function getMock(): Client
    {
        return $this->mock;
    }

    public function expectSearch(array $input, FakeElasticResponse $response): void
    {
        $this->mock
            ->expects('search')
            ->with($input)
            ->andReturn($response);
    }
}
