<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Application;

use JeroenG\Explorer\Domain\IndexManagement\IndexConfigurationInterface;

interface IndexAdapterInterface
{
    public function getActualConfiguration(IndexConfigurationInterface $indexConfiguration): ?IndexConfigurationInterface;

    public function create(IndexConfigurationInterface $indexConfiguration): void;

    public function pointToAlias(IndexConfigurationInterface $indexConfiguration): void;

    public function delete(IndexConfigurationInterface $indexConfiguration): void;

    public function flush(string $index): void;
}
