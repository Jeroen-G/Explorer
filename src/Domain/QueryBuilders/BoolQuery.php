<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\QueryBuilders;

use Illuminate\Support\Collection;
use JeroenG\Explorer\Domain\Syntax\SyntaxInterface;
use InvalidArgumentException;
use Webmozart\Assert\Assert;

class BoolQuery implements QueryBuilderInterface
{
    private Collection $must;
    private Collection $should;
    private Collection $filter;

    public function __construct()
    {
        $this->must = new Collection();
        $this->should = new Collection();
        $this->filter = new Collection();
    }

    public function add(string $type, SyntaxInterface $syntax): void
    {
        switch ($type) {
            case QueryType::MUST:
                $this->must->add($syntax);
                return;
            case QueryType::SHOULD:
                $this->should->add($syntax);
                return;
            case QueryType::FILTER:
                $this->filter->add($syntax);
                return;
            default:
                throw new InvalidArgumentException($type.' is not a valid type.');
        }
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
        return [
            'bool' => [
                'must' => $this->must->map(fn($must) => $must->build())->toArray(),
                'should' => $this->should->map(fn($should) => $should->build())->toArray(),
                'filter' => $this->filter->map(fn($filter) => $filter->build())->toArray(),
            ],
        ];
    }
}
