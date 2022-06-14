<?php

declare(strict_types=1);


namespace JeroenG\Explorer\Domain\Syntax;

use Webmozart\Assert\Assert;
use JeroenG\Ontology\Domain\Attributes as DDD;

#[DDD\Www('Official documentation', 'https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-query-string-query.html')]
class QueryString implements SyntaxInterface
{
    public const OP_AND = 'AND';

    public const OP_OR = 'OR';

    protected string $queryString;

    protected float $boost;

    protected string $defaultOperator;

    public function __construct(string $queryString, string $defaultOperator = self::OP_OR, float $boost = 1.0)
    {
        Assert::oneOf($defaultOperator, [self::OP_OR, self::OP_AND]);

        $this->queryString = $queryString;
        $this->boost = $boost;
        $this->defaultOperator = $defaultOperator;
    }

    public function build(): array
    {
        return [
            'query_string' => [
                'query' => $this->queryString,
                'default_operator' => $this->defaultOperator,
                'boost' => $this->boost,
            ],
        ];
    }
}
