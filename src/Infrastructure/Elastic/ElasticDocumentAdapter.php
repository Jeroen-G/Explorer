<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\Elastic;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use JeroenG\Explorer\Application\DocumentAdapterInterface;
use JeroenG\Explorer\Application\Operations\Bulk\BulkOperationInterface;
use JeroenG\Explorer\Application\Results;
use JeroenG\Explorer\Application\SearchCommandInterface;

final class ElasticDocumentAdapter implements DocumentAdapterInterface
{
    public function __construct(
        private Client $client,
    ) {
    }

    public function bulk(BulkOperationInterface $command): callable|array
    {
        return $this->client->bulk([
            'body' => $command->build(),
        ])->asArray();
    }

    public function update(string $index, $id, array $data): callable|array
    {
        return $this->client->index([
            'index' => $index,
            'id' => $id,
            'body' => $data,
        ])->asArray();
    }

    public function delete(string $index, $id): void
    {
        try {
            $this->client->delete([
                'index' => $index,
                'id' => $id
            ]);
        } catch (ClientResponseException $clientResponseException) {
            if ($clientResponseException->getCode() === 404) {
                return;
            }

            throw $clientResponseException;
        }
    }

    public function search(SearchCommandInterface $command): Results
    {
        return (new Finder($this->client, $command))->find();
    }
}
