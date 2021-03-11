<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Application;

use Elasticsearch\Client;
use JeroenG\Explorer\Domain\Compound\QueryType;
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
        $compound = $this->builder->getCompound();

        $compound->addMany(QueryType::MUST, $this->builder->getMust());
        $compound->addMany(QueryType::SHOULD, $this->builder->getShould());
        $compound->addMany(QueryType::FILTER, $this->builder->getFilter());

        if (!empty($this->builder->getQuery())) {
            $compound->add('must', new MultiMatch($this->builder->getQuery(), $this->builder->getDefaultSearchFields()));
        }

        foreach ($this->builder->getWhere() as $field => $value) {
            $compound->add('must', new Term($field, $value));
        }

        $query = [
            'index' => $this->builder->getIndex(),
            'body' => [
                'query' => $compound->build(),
            ],
        ];

        if ($this->builder->getOffset() && $this->builder->getLimit()) {
            $query['from'] = $this->builder->getOffset();
            $query['size'] = $this->builder->getLimit();
        }

        if ($this->builder->hasSort()) {
            $query['body']['sort'] = $this->builder->getSort();
        }

        $rawResults = $this->client->search($query);

        return new Results($rawResults);
    }
}
