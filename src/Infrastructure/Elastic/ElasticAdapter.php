<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\Elastic;

use Elasticsearch\Client;
use JeroenG\Explorer\Application\IndexAdapterInterface;
use JeroenG\Explorer\Application\Operations\Bulk\BulkOperationInterface;
use JeroenG\Explorer\Application\Results;
use JeroenG\Explorer\Application\SearchCommandInterface;

class ElasticAdapter implements IndexAdapterInterface
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function bulk(BulkOperationInterface $command)
    {
        return $this->client->bulk([ 'body' => $command->build() ]);
    }

    public function update(string $index, $id, array $data)
    {
        return $this->client->index([
            'index' => $index,
            'id' => $id,
            'body' => $data,
        ]);
    }

    public function flush(string $index)
    {
        $matchAllQuery = [ 'query' => [ 'match_all' => (object)[] ] ];
        $this->client->deleteByQuery([
            'index' => $index,
            'body' => $matchAllQuery
        ]);
    }

    public function delete(string $index, $id)
    {
        $this->client->delete([
            'index' => $index,
            'id' => $id
        ]);
    }

    public function search(SearchCommandInterface $command): Results
    {
        $finder = new Finder($this->client, $command);
        return $finder->find();
    }
}
