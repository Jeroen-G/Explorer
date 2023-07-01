<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Domain\Query\QueryProperties;

use JeroenG\Explorer\Domain\Query\QueryProperties\Sorting;
use JeroenG\Explorer\Domain\Syntax\Sort;
use PHPUnit\Framework\TestCase;

final class SortingTest extends TestCase
{
    public function test_it_builds_sorting(): void
    {
        $sort = Sorting::for(
            new Sort(':fld:', Sort::DESCENDING),
        );

        self::assertSame([ 'sort' => [ [ ':fld:' => 'desc' ]]],$sort->build());
    }

    public function test_it_combines(): void
    {
        $a = Sorting::for(
            new Sort(':fld1:', Sort::DESCENDING),
            new Sort(':fld2:', Sort::DESCENDING),
        );
        $b = Sorting::for(
            new Sort(':fld3:', Sort::DESCENDING),
            new Sort(':fld4:', Sort::DESCENDING),
        );
        $c = Sorting::for(
            new Sort(':fld5:', Sort::DESCENDING),
        );
        $d = Sorting::for();

        $result = $a->combine($b, $c, $d);

        self::assertNotSame($a->build(), $result->build());
        self::assertSame([
            'sort' => [
                [ ':fld1:' => 'desc' ],
                [ ':fld2:' => 'desc' ],
                [ ':fld3:' => 'desc' ],
                [ ':fld4:' => 'desc' ],
                [ ':fld5:' => 'desc' ],
            ],
        ], $result->build());
    }
}
