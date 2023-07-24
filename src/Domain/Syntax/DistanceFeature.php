<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

use JeroenG\Explorer\Domain\Syntax\SyntaxInterface;

class DistanceFeature implements SyntaxInterface
{
    private string $field;

    private ?float $boost;

    private string $pivot;

    private mixed $origin;

    public function __construct(
        string $field, 
        $pivot,
        $origin, 
        ?float $boost = 1.0
    ){
        $this->field = $field;
        $this->pivot = $pivot;
        $this->origin = $origin;
        $this->boost = $boost;
    }

    public function build(): array
    {
        return [
            'distance_feature' => [
                'field' => $this->field,
                'pivot' => $this->pivot,
                'origin' => $this->origin,
                'boost' => $this->boost,
            ]
        ];
    }
}
