<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Application\Operations\Bulk;

interface BulkOperationInterface
{
    public function build(): array;
}
