<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

use Webmozart\Assert\Assert;
use JeroenG\Ontology\Domain\Attributes as DDD;

#[DDD\Www('Official documentation', 'https://www.elastic.co/guide/en/elasticsearch/reference/current/sort-search-results.html')]
class Sort implements SyntaxInterface
{
    public const ASCENDING = 'asc';

    public const DESCENDING = 'desc';

    private string $field;

    private string $order;

    public function __construct(string $field, string $order = self::ASCENDING)
    {
        $this->field = $field;
        $this->order = $order;
        Assert::inArray($order, [self::ASCENDING, self::DESCENDING]);
    }

    public function build(): array
    {
        return [$this->field => $this->order];
    }
}
