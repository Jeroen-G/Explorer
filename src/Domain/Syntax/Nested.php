<?php

declare(strict_types=1);


namespace JeroenG\Explorer\Domain\Syntax;

use JeroenG\Ontology\Domain\Attributes as DDD;

#[DDD\Www('Official documentation', 'https://www.elastic.co/guide/en/elasticsearch/reference/current/nested.html')]
class Nested implements SyntaxInterface
{
    private string $path;

    private SyntaxInterface $query;

    public function __construct(string $path, SyntaxInterface $syntax)
    {
        $this->path = $path;
        $this->query = $syntax;
    }

    public function build(): array
    {
        return [
            'nested' => [
                'path' => $this->path,
                'query' => $this->query->build(),
            ],
        ];
    }
}
