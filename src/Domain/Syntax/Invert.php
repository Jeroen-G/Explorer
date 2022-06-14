<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

use JeroenG\Ontology\Domain\Attributes as DDD;

#[DDD\Www('Official documentation', 'https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html')]
class Invert implements SyntaxInterface
{
    private SyntaxInterface $query;

    public static function query(SyntaxInterface $syntax): self
    {
        $query = new self();
        $query->query = $syntax;
        return $query;
    }

    public function build(): array
    {
        return [
            'bool' => [
                'must_not' => $this->query->build(),
            ],
        ];
    }
}
