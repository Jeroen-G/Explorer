<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\Elastic;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use JeroenG\Explorer\Application\DocumentAdapterInterface;
use JeroenG\Explorer\Application\Operations\Bulk\BulkOperationInterface;
use JeroenG\Explorer\Application\Results;
use JeroenG\Explorer\Application\SearchCommandInterface;
use Psr\Log\LoggerInterface;

final class ElasticDocumentAdapter implements DocumentAdapterInterface
{
    public function __construct(
        private Client $client,
        private LoggerInterface $logger,
    ) {
    }

    public function bulk(BulkOperationInterface $command): callable|array
    {
        $response = $this->client->bulk([
            'body' => $command->build(),
        ])->asArray();

        // Check for bulk operation errors and log them
        if (isset($response['errors']) && $response['errors'] === true) {
            $this->logBulkErrors($response);
        }

        return $response;
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

    private function logBulkErrors(array $response): void
    {
        if (!isset($response['items']) || !is_array($response['items'])) {
            return;
        }

        foreach ($response['items'] as $item) {
            foreach ($item as $operation => $result) {
                if (isset($result['error'])) {
                    $errorChain = $this->buildErrorChain($result['error']);

                    $this->logger->error('Elasticsearch bulk operation error', [
                        'operation' => $operation,
                        'index' => $result['_index'] ?? 'unknown',
                        'id' => $result['_id'] ?? 'unknown',
                        'status' => $result['status'] ?? 'unknown',
                        'error_type' => $result['error']['type'] ?? 'unknown',
                        'error_reason' => $result['error']['reason'] ?? 'unknown',
                        'error_chain' => $errorChain,
                        'full_error' => $result['error'],
                    ]);
                }
            }
        }
    }

    private function buildErrorChain(array $error): string
    {
        $errorStrings = [];
        $currentError = $error;

        do {
            $errorStrings[] = sprintf(
                'Type: %s, Reason: %s',
                $currentError['type'] ?? 'unknown',
                $currentError['reason'] ?? 'unknown'
            );
            $currentError = $currentError['caused_by'] ?? null;
        } while ($currentError !== null);

        return implode(' → ', $errorStrings);
    }
}
