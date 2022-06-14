<?php

declare(strict_types=1);


namespace JeroenG\Explorer\Domain\Syntax;

use JeroenG\Ontology\Domain\Attributes as DDD;

#[DDD\Www('Official documentation', 'https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-simple-query-string-query.html')]
class SimpleQueryString extends QueryString
{
    public function build(): array
    {
        return [
            'simple_query_string' => [
                'query' => $this->queryString,
                'default_operator' => $this->defaultOperator,
                'boost' => $this->boost,
            ],
        ];
    }
}
