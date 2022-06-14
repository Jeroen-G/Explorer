<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

use JeroenG\Ontology\Domain\Attributes as DDD;

#[DDD\Www('Official documentation', 'https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-exists-query.html')]
class Exists implements SyntaxInterface
{
    private string $field;

    public static function field(string $field): self
    {
        $exists = new self();
        $exists->field = $field;
        return $exists;
    }

    public function build(): array
    {
        return [
            'exists' => [ 'field' => $this->field ],
        ];
    }
}
