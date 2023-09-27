<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

use Webmozart\Assert\Assert;

class Sort implements SyntaxInterface
{
    public const ASCENDING = 'asc';

    public const DESCENDING = 'desc';

    private array $sortCollection = [];

    public function __construct(mixed $fieldOrArray, ?string $order = self::ASCENDING)
    {
        if (is_array($fieldOrArray)) {
            foreach($fieldOrArray as $sortField => $sortOrder) {
                Assert::inArray($sortOrder, [self::ASCENDING, self::DESCENDING]);
                $this->sortCollection[] = [$sortField => $sortOrder];
            }
        } else {
            Assert::inArray($order, [self::ASCENDING, self::DESCENDING]);
            $this->sortCollection[] = [$fieldOrArray => $order];
        }
    }

    public function build(): array
    {
        return $this->sortCollection;
    }
}
