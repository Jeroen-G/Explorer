<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax\Compound;

use JeroenG\Explorer\Domain\Syntax\Compound\ScoreFunction\ScoreFunction;
use JeroenG\Explorer\Domain\Syntax\MatchAll;
use JeroenG\Explorer\Domain\Syntax\SyntaxInterface;

class FunctionScore implements SyntaxInterface
{
    public const BOOST_MULTIPLY = 'multiply';

    public const BOOST_SUM = 'sum';

    public const BOOST_AVG = 'avg';

    public const BOOST_FIRST = 'first';

    public const BOOST_MAX = 'max';

    public const BOOST_MIN = 'min';

    public const BOOST_REPLACE = 'replace';

    public const SCORE_MULTIPLY = 'multiply';

    public const SCORE_SUM = 'sum';

    public const SCORE_AVG = 'avg';

    public const SCORE_FIRST = 'first';

    public const SCORE_MAX = 'max';

    public const SCORE_MIN = 'min';

    private SyntaxInterface $query;

    private array $functions = [];

    private string $boostMode = self::BOOST_MULTIPLY;

    private string $scoreMode = self::SCORE_MULTIPLY;

    private ?int $maxBoost = null;

    private ?int $minScore = null;

    private ?int $weight = null;

    public function __construct()
    {
        $this->query = new MatchAll();
    }

    public function build(): array
    {
        $functions = array_map(fn ($function) => $function->build(), $this->functions);

        $query = [ 'query' => $this->query->build() ];
        if (!empty($functions)) {
            $query['functions'] = $functions;
        }
        if (!is_null($this->minScore)) {
            $query['min_score'] = $this->minScore;
        }
        if (!is_null($this->maxBoost)) {
            $query['max_boost'] = $this->maxBoost;
        }
        if (!is_null($this->weight)) {
            $query['weight'] = $this->weight;
        }
        $query['boost_mode'] = $this->boostMode;
        $query['score_mode'] = $this->scoreMode;

        return [
            'function_score' => $query
        ];
    }

    public function addFunction(ScoreFunction $function): void
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

    public function setQuery(SyntaxInterface $query): void
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

    public function setWeight(?int $weight): void
    {
        $this->weight = $weight;
    }
}
