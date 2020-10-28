<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Application;

use Elasticsearch\Client;
use JeroenG\Explorer\Domain\QueryBuilders\BoolQuery;
use JeroenG\Explorer\Domain\QueryBuilders\QueryType;
use JeroenG\Explorer\Domain\Syntax\MultiMatch;
use JeroenG\Explorer\Domain\Syntax\Term;

class Finder
{
    private Client $client;

    private BuildCommand $builder;

    public function __construct(Client $client, BuildCommand $builder)
    {
        $this->client = $client;
        $this->builder = $builder;
    }

    public function find(): Results
    {
        $aggregate = new BoolQuery();

        $aggregate->addMany(QueryType::MUST, $this->builder->getMust());
        $aggregate->addMany(QueryType::SHOULD, $this->builder->getShould());
        $aggregate->addMany(QueryType::FILTER, $this->builder->getFilter());

        if ($this->builder->getQuery() !== '') {
            $aggregate->add('must', new MultiMatch($this->builder->getQuery()));
        }

        foreach($this->builder->getWhere() as $field => $value) {
            $aggregate->add('must', new Term($field, $value));
        }

        $query = [
            'index' => $this->builder->getIndex(),
            'body'  => [
                'query' => $aggregate->build(),
            ],
        ];

        $rawResults = $this->client->search($query);

        return new Results($rawResults);
    }
}
