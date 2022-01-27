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

    private ?array $filter = null;

    private bool $nested = false;

    public function __construct(string $field, string $order = self::ASCENDING, $filter = null, $nested = false)
    {
        $this->field = $field;
        $this->order = $order;
        $this->filter = $filter;
        $this->nested = $nested;
        Assert::inArray($order, [self::ASCENDING, self::DESCENDING]);
    }

    public function build(): array
    {
        $parts = explode('.', $this->field);
        if (count($parts) > 1 && $this->nested) {
            $path = implode('.', array_slice($parts, 0, count($parts) - 1));

            $sort = [$this->field => [
                'order' => $this->order,
                'nested' => [
                    'path' => $path,
                ],
            ]];
            if (null !== $this->filter) {
                $sort[$this->field]['nested']['filter'] = [
                    'term' => $this->filter
                ];
            }

            return $sort;
        } else {
            return [$this->field => $this->order];
        }
    }
}
