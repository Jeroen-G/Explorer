<?php

declare(strict_types=1);


namespace JeroenG\Explorer\Domain\Syntax;

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
