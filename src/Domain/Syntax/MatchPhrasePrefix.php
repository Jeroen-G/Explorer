<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

class MatchPhrasePrefix implements SyntaxInterface
{
    private string $field;

    private mixed $value;

    public function __construct(string $field, $value = null)
    {
        $this->field = $field;
        $this->value = $value;
    }

    public function build(): array
    {
        $query = [ 'query' => $this->value ];

        return ['match_phrase_prefix' => [ $this->field => $query ] ];
    }
}
