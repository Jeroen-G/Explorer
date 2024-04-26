<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Aggregations;

final class NestedFilteredAggregation implements AggregationSyntaxInterface
{
    private string $path;

    private string $name;

    private string $field;

    /**
     * @var array<string, mixed>
     */
    private array $filters;

    private int $size;

    /**
     * @param array<string, mixed> $filters
     */
    public function __construct(string $path, string $name, string $field, array $filters, int $size = 10)
    {
        $this->path = $path;
        $this->name = $name;
        $this->field = $field;
        $this->size = $size;
        $this->filters = $filters;
    }

    /**
     * @return array<string, mixed>
     */
    public function build(): array
    {
        return [
            'nested' => [
                'path' => $this->path,
            ],
            'aggs' => [
                'filter_aggs' => [
                    'filter' => $this->buildElasticFilters(),
                    'aggs' => [
                        $this->name => [
                            'terms' => [
                                'field' => $this->path . '.' . $this->field,
                                'size' => $this->size,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildElasticFilters(): array
    {
        $elasticFilters = [];
        foreach ($this->filters as $field => $filterValues) {
            $elasticFilters[] = [
                'terms' => [
                    $this->path . '.' . $field => $filterValues,
                ],
            ];
        }

        return [
            'bool' => [
                'should' => [
                    'bool' => [
                        'must' => $elasticFilters,
                    ],
                ],
            ],
        ];
    }
}
