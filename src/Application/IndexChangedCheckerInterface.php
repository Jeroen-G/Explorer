<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Application;

use JeroenG\Explorer\Domain\IndexManagement\IndexConfigurationInterface;

interface IndexChangedCheckerInterface
{
    public function hasChanges(IndexConfigurationInterface $targetConfig): bool;
}
