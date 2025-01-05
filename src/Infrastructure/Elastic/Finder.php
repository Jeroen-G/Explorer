<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\Elastic;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientInterface;
use JeroenG\Explorer\Application\Results;
use JeroenG\Explorer\Application\SearchCommandInterface;

class Finder
{
    /**
     * @param Client $client
     */
    public function __construct(
        private ClientInterface $client,
        private SearchCommandInterface $builder,
    ) {
    }

    public function find(): Results
    {
        $query = [
            'index' => $this->builder->getIndex(),
            'body' => $this->builder->buildQuery(),
        ];

        $rawResults = $this->client->search($query)->asArray();

        return new Results($rawResults);
    }
}
