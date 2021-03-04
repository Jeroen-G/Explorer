<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\IndexManagement;

interface IndexConfigurationInterface
{
    public function name(): string;

    public function properties(): array;

    public function settings(): array;
}
