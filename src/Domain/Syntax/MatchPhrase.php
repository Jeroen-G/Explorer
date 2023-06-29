<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

class MatchPhrase implements SyntaxInterface
{
    private string $field;

    private $value;

    private ?float $boost = null;

    public function __construct(string $field, $value = null, $boost = null)
    {
        $this->field = $field;
        $this->value = $value;

        if ($boost) {
            $this->boost = $boost;
        }
    }

    public function build(): array
    {
        $query = [ 'query' => $this->value ];

        if (!is_null($this->boost)) {
            $query['boost'] = $this->boost;
        }

        return ['match_phrase' => [ $this->field => $query ] ];
    }

    public function setBoost(float $boost): void
    {
        $this->boost = $boost;
    }
}
