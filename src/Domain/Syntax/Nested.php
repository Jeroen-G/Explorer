<?php

declare(strict_types=1);


namespace JeroenG\Explorer\Domain\Syntax;

class Nested implements SyntaxInterface
{
    private string $path;

    private SyntaxInterface $query;

    private array $options;

    public function __construct(string $path, SyntaxInterface $syntax, array $options = [])
    {
        $this->path = $path;
        $this->query = $syntax;
        $this->options = $options;
    }

    public function build(): array
    {
        $data = [
            'path' => $this->path,
            'query' => $this->query->build(),
        ];
        if (isset($this->options['ignore_unmapped'])) {
            $data['ignore_unmapped'] = $this->options['ignore_unmapped'];
        }
        return ['nested' => $data];
    }
}