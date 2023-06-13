<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

use JeroenG\Explorer\Domain\Syntax\SyntaxInterface;

class RankFeature implements SyntaxInterface
{
    private string $field;

    private ?float $boost;

    private ?array $function;

    public function __construct(
        string $field,
        ?float $boost = 1.0,
        ?array $function = null
    ){
        $this->field = $field;
        $this->boost = $boost;
        $this->function = $function;
    }

    public function build(): array
    {
        $rank_object = [
            'field' => $this->field,
            'boost' => $this->boost,
        ];

        if ($this->function) {
            $rank_object[ $this->function['type'] ] = $this->function['content'];
        }
        
        return [
            'rank_feature' => $rank_object
        ];
    }
}
