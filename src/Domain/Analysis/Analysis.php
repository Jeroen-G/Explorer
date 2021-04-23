<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Analysis;

use JeroenG\Explorer\Domain\Analysis\Analyzer\AnalyzerInterface;
use JeroenG\Explorer\Domain\Analysis\Filter\FilterInterface;

final class Analysis
{
    private array $analyzers = [];

    private array $filters = [];

    public function addAnalyzer(AnalyzerInterface $analyzer): self
    {
        $this->analyzers[$analyzer->getName()] = $analyzer->build();

        return $this;
    }

    public function addFilter(FilterInterface $filter): self
    {
        $this->filters[$filter->getName()] = $filter->build();

        return $this;
    }

    public function build(): array
    {
        return [
            'analysis' => [
                'analyzer' => $this->analyzers,
                'filter' => $this->filters,
            ],
        ];
    }
}
