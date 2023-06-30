<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Query\QueryProperties;

use JeroenG\Explorer\Domain\Syntax\SyntaxInterface;

class Rescoring
{
    public const SCORE_MODE_TOTAL = 'total';

    public const SCORE_MODE_MULTIPLY = 'multiply';

    public const SCORE_MODE_AVERAGE = 'avg';

    public const SCORE_MODE_MAX = 'max';

    public const SCORE_MODE_MIN = 'min';

    private int $windowSize = 10;

    private string $scoreMode = Rescoring::SCORE_MODE_TOTAL;

    private float $queryWeight = 1;

    private float $rescoreQueryWeight = 1;

    private SyntaxInterface $query;

    public static function create(SyntaxInterface $query): self
    {
        $rescore = new self();
        $rescore->query = $query;
        return $rescore;
    }

    public function build(): array
    {
        return [
            'window_size' => $this->windowSize,
            'query' => [
                'score_mode' => $this->scoreMode,
                'rescore_query' => $this->query->build(),
                'query_weight' => $this->queryWeight,
                'rescore_query_weight' => $this->rescoreQueryWeight,
            ],
        ];
    }

    public function setWindowSize(int $windowSize): void
    {
        $this->windowSize = $windowSize;
    }

    public function setScoreMode(string $scoreMode): void
    {
        $this->scoreMode = $scoreMode;
    }

    public function setQueryWeight($queryWeight): void
    {
        $this->queryWeight = $queryWeight;
    }

    public function setRescoreQueryWeight($rescoreQueryWeight): void
    {
        $this->rescoreQueryWeight = $rescoreQueryWeight;
    }

    public function setQuery(SyntaxInterface $query): void
    {
        $this->query = $query;
    }
}
