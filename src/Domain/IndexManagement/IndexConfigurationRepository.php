<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\IndexManagement;

interface IndexConfigurationRepository
{
    /**
     * @return iterable<IndexConfiguration>
     */
    public function getConfigurations(): iterable;
}
