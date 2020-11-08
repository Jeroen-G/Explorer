<?php

declare(strict_types=1);


namespace JeroenG\Explorer\Domain\Syntax;

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
