<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Analysis\Analyzer;

use JeroenG\Explorer\Domain\Analysis\Filter\FilterInterface;

final class StandardAnalyzer implements AnalyzerInterface
{
    private string $name;

    private string $tokenizer = 'standard';

    private array $filters = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setFilters(array $filters = []): void
    {
        $this->filters = $filters;
    }

    public function getFilters(): array
    {
        return array_map(function ($filter) {
            if ($filter instanceof FilterInterface) {
                return $filter->getName();
            }

            return $filter;
        }, $this->filters);
    }

    public function build(): array
    {
        return [
            'tokenizer' => $this->tokenizer,
            'filter' => $this->getFilters(),
        ];
    }
}
