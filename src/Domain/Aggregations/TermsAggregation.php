<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Aggregations;

final class TermsAggregation implements AggregationSyntaxInterface
{
    /** @var AggregationSyntaxInterface [] */
    private array $aggs;

    private string $field;

    private ?int $size;

    public function __construct(
        string $field,
        int $size = null,
        array $aggs = []
    )
    {
        $this->field = $field;
        $this->size = $size;
        $this->aggs = $aggs;

    }

    public function build(): array
    {
        $terms = ['terms' => ['field' => $this->field]];
        if ($this->size !== null) {
            $terms['terms']['size'] = $this->size;
        }
        if (count($this->aggs)) {
            foreach ($this->aggs as $key => $value) {
                $terms['aggs'][$key] = $value->build();
            }
        }
        return $terms;
    }
}
