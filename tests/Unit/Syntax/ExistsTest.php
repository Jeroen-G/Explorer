<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Syntax;

use JeroenG\Explorer\Domain\Syntax\Exists;
use PHPUnit\Framework\TestCase;

class ExistsTest extends TestCase
{
    public function test_it_builds_exists(): void
    {
        $exists = Exists::field(':fld:');

        self::assertEquals(['exists' => [ 'field' => ':fld:' ]], $exists->build());
    }
}
