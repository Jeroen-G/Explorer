<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\IndexManagement;

class IndexConfigurationNotFoundException extends \InvalidArgumentException
{
    public static function index(string $name): IndexConfigurationNotFoundException
    {
        return new self("The configuration for index $name could not be found.");
    }
}
