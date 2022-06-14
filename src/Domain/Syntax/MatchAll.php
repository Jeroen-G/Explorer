<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

use JeroenG\Ontology\Domain\Attributes as DDD;

#[DDD\Www('Official documentation', 'https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-all-query.html')]
class MatchAll implements SyntaxInterface
{
    public function build(): array
    {
        return ['match_all' => (object)[] ];
    }
}
