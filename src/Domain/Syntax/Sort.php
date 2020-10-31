<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

use Webmozart\Assert\Assert;

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
