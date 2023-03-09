<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\Elastic;

use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use JeroenG\Explorer\Application\DocumentAdapterInterface;
use JeroenG\Explorer\Application\Operations\Bulk\BulkOperationInterface;
use JeroenG\Explorer\Application\Results;
use JeroenG\Explorer\Application\SearchCommandInterface;

final class ElasticDocumentAdapter implements DocumentAdapterInterface
{
    private Client $client;

    public function __construct(ElasticClientFactory $clientFactory)
    {
        $this->client = $clientFactory->client();
    }

    public function bulk(BulkOperationInterface $command): callable|array
    {
        return $this->client->bulk([
            'body' => $command->build(),
        ]);
    }

    public function update(string $index, $id, array $data): callable|array
    {
        return $this->client->index([
            'index' => $index,
            'id' => $id,
            'body' => $data,
        ]);
    }

    public function delete(string $index, $id): void
    {
        try {
            $this->client->delete([
                'index' => $index,
                'id' => $id
            ]);
        } catch (Missing404Exception) {}
    }

    public function search(SearchCommandInterface $command): Results
    {
        return (new Finder($this->client, $command))->find();
    }
}
