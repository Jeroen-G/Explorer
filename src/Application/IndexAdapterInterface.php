<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Application;

use JeroenG\Explorer\Domain\IndexManagement\IndexConfigurationInterface;

interface IndexAdapterInterface
{
    public function getRemoteConfiguration(IndexConfigurationInterface $indexConfiguration): ?IndexConfigurationInterface;

    public function getInactiveIndexForAlias(IndexConfigurationInterface $indexConfiguration): ?string;

    public function create(IndexConfigurationInterface $indexConfiguration): void;

    public function pointToAlias(IndexConfigurationInterface $indexConfiguration): void;

    public function delete(IndexConfigurationInterface $indexConfiguration): void;

    public function flush(string $index): void;

    public function createNewInactiveIndex(IndexConfigurationInterface $indexConfiguration): string;
}
