<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

class Sort
{
    /** @deprecated Use SortOrder::ASCENDING instead */
    public const ASCENDING = 'asc';
    
    /** @deprecated Use SortOrder::DESCENDING instead */
    public const DESCENDING = 'desc';

    private string $field;

    private SortOrder $order;

    public function __construct(string $field, string|SortOrder $order = SortOrder::ASCENDING)
    {
        $this->field = $field;
        
        if (is_string($order)) {
            $this->order = SortOrder::fromString($order);
        } else {
            $this->order = $order;
        }
    }

    public function build(): array
    {
        return [$this->field => $this->order->build()];
    }
}
