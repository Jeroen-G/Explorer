<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\IndexManagement;

interface AliasedIndexConfigurationInterface extends IndexConfigurationInterface
{
    public function getAliasConfiguration(): IndexAliasConfigurationInterface;
}
