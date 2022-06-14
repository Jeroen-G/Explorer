<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

use Webmozart\Assert\Assert;
use JeroenG\Ontology\Domain\Attributes as DDD;

#[DDD\Www('Official documentation', 'https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-terms-query.html')]
class Terms implements SyntaxInterface
{
    private string $field;

    private array $values;

    private ?float $boost;

    public function __construct(string $field, array $values = [], ?float $boost = 1.0)
    {
        Assert::allStringNotEmpty($values);

        $this->field = $field;
        $this->values = $values;
        $this->boost = $boost;
    }

    public function build(): array
    {
        return ['terms' => [
            $this->field => $this->values,
            'boost' => $this->boost,
        ]];
    }
}
