<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax\Compound;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use JeroenG\Explorer\Domain\Syntax\SyntaxInterface;
use Webmozart\Assert\Assert;

class BoolQuery implements SyntaxInterface
{
    private Collection $must;

    private Collection $should;

    private Collection $filter;

    private ?string $minimumShouldMatch = null;

    public function __construct()
    {
        $this->must = new Collection();
        $this->should = new Collection();
        $this->filter = new Collection();
    }

    public function add(string $type, SyntaxInterface $syntax): void
    {
        match ($type) {
           QueryType::MUST => $this->must->add($syntax),
           QueryType::SHOULD => $this->should->add($syntax),
           QueryType::FILTER => $this->filter->add($syntax),
           default => throw new InvalidArgumentException($type . ' is not a valid type.'),
        };
    }

    public function must(SyntaxInterface $syntax): void
    {
        $this->must->add($syntax);
    }

    public function should(SyntaxInterface $syntax): void
    {
        $this->should->add($syntax);
    }

    public function filter(SyntaxInterface $syntax): void
    {
        $this->filter->add($syntax);
    }

    public function minimumShouldMatch(?string $value): void
    {
        $this->minimumShouldMatch = $value;
    }

    public function addMany(string $type, array $syntax): void
    {
        Assert::allIsInstanceOf($syntax, SyntaxInterface::class);

        foreach ($syntax as $item) {
            $this->add($type, $item);
        }
    }

    public function build(): array
    {
        $boolQuery = [
            'must' => $this->must->map(fn ($must) => $must->build())->toArray(),
            'should' => $this->should->map(fn ($should) => $should->build())->toArray(),
            'filter' => $this->filter->map(fn ($filter) => $filter->build())->toArray(),
        ];

        if (!is_null($this->minimumShouldMatch)) {
            $boolQuery['minimum_should_match'] = $this->minimumShouldMatch;
        }

        return [
            'bool' => $boolQuery
        ];
    }

    public function clone(): self
    {
        $query = new BoolQuery();

        $query->must = clone $this->must;
        $query->should = clone $this->should;
        $query->filter = clone $this->filter;
        $query->minimumShouldMatch = $this->minimumShouldMatch;

        return $query;
    }
}
