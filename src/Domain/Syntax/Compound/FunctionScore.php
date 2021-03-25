<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax\Compound;

use JeroenG\Explorer\Domain\Syntax\Compound\ScoreFunction\ScoreFunction;
use JeroenG\Explorer\Domain\Syntax\MatchAll;
use JeroenG\Explorer\Domain\Syntax\SyntaxInterface;

class FunctionScore implements SyntaxInterface
{
    const BOOST_MULTIPLY = 'multiply';
    const BOOST_SUM = 'sum';
    const BOOST_AVG = 'avg';
    const BOOST_FIRST = 'first';
    const BOOST_MAX = 'max';
    const BOOST_MIN = 'min';
    const BOOST_REPLACE = 'replace';

    const SCORE_MULTIPLY = 'multiply';
    const SCORE_SUM = 'sum';
    const SCORE_AVG = 'avg';
    const SCORE_FIRST = 'first';
    const SCORE_MAX = 'max';
    const SCORE_MIN = 'min';

    private SyntaxInterface $query;

    private array $functions = [];

    private string $boostMode = self::BOOST_MULTIPLY;

    private string $scoreMode = self::SCORE_MULTIPLY;

    private ?int $maxBoost = null;

    private ?int $minScore = null;

    public function __construct()
    {
        $this->query = new MatchAll();
    }

    public function build(): array
    {
        $functions = array_map(fn ($function) => $function->build(), $this->functions);

        $query = [ 'query' =>  $this->query->build() ];
        if (!empty($functions)) {
            $query['functions'] = $functions;
        }
        if ($this->minScore !== null) {
            $query['min_score'] = $this->minScore;
        }
        if ($this->maxBoost !== null) {
            $query['max_boost'] = $this->maxBoost;
        }
        $query['boost_mode'] = $this->boostMode;
        $query['score_mode'] = $this->scoreMode;

        return [
            'function_score' => $query
        ];
    }

    public function addFunction(ScoreFunction $function)
    {
        $this->functions[] = $function;
    }

    public function setScoreMode(string $scoreMode): void
    {
        $this->scoreMode = $scoreMode;
    }

    public function setBoostMode(string $boostMode): void
    {
        $this->boostMode = $boostMode;
    }

    public function setQuery(?SyntaxInterface $query): void
    {
        $this->query = $query;
    }

    public function setMinScore(?int $minScore): void
    {
        $this->minScore = $minScore;
    }

    public function setMaxBoost(?int $maxBoost): void
    {
        $this->maxBoost = $maxBoost;
    }
}
