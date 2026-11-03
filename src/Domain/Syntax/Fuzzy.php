<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

class Fuzzy implements SyntaxInterface
{
    private string $field;

    private string $value;

    private string|int $fuzziness;

    private int $maxExpansions;

    private int $prefixLength;

    private bool $transpositions;

    private string $rewrite;

    private float $boost;

    public function __construct(
        string $field,
        string $value,
        string|int $fuzziness = 'AUTO',
        int $maxExpansions = 50,
        int $prefixLength = 0,
        bool $transpositions = true,
        string $rewrite = 'constant_score',
        float $boost = 1.0,
    ) {
        $this->field = $field;
        $this->value = $value;
        $this->fuzziness = $fuzziness;
        $this->maxExpansions = $maxExpansions;
        $this->prefixLength = $prefixLength;
        $this->transpositions = $transpositions;
        $this->rewrite = $rewrite;
        $this->boost = $boost;
    }

    public function build(): array
    {
        return ['fuzzy' => [
            $this->field => [
                'value' => $this->value,
                'fuzziness' => $this->fuzziness,
                'max_expansions' => $this->maxExpansions,
                'prefix_length' => $this->prefixLength,
                'transpositions' => $this->transpositions,
                'rewrite' => $this->rewrite,
                'boost' => $this->boost,
            ],
        ]];
    }
}
