<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax\Compound\ScoreFunction;

class ScriptScoreFunction extends ScoreFunction
{
    private string $source;

    private array $params = [];

    public function build(): array
    {
        return array_merge([
            'script_score' => [
                'script' => [
                    'params' => $this->params,
                    'source' => $this->source,
                ],
            ],
        ], parent::build());
    }

    public function setSource(string $source): void
    {
        $this->source = $source;
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }
}
