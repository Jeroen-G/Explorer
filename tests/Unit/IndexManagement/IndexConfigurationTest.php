<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\IndexManagement;

use JeroenG\Explorer\Domain\IndexManagement\IndexConfiguration;
use PHPUnit\Framework\TestCase;

class IndexConfigurationTest extends TestCase
{
    public function test_it_can_be_constructed_with_empty_configuration(): void
    {
        $config = IndexConfiguration::empty('test');

        self::assertSame('test', $config->name());
        self::assertSame([], $config->properties());
        self::assertSame([], $config->settings());
    }
}
