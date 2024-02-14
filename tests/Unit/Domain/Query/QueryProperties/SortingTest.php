<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Domain\Query\QueryProperties;

use JeroenG\Explorer\Domain\Query\QueryProperties\Sorting;
use JeroenG\Explorer\Domain\Syntax\Sort;
use JeroenG\Explorer\Domain\Syntax\SortOrder;
use PHPUnit\Framework\TestCase;

final class SortingTest extends TestCase
{
    public function test_it_builds_sorting(): void
    {
        $sort = Sorting::for(
            new Sort(':fld:', SortOrder::DESCENDING),
        );

        self::assertSame([ 'sort' => [ [ ':fld:' => 'desc' ]]],$sort->build());
    }
    
    public function test_it_builds_sorting_from_sort_order(): void
    {
        $sort = Sorting::for(
            new Sort(':fld:', SortOrder::for(SortOrder::DESCENDING, SortOrder::MISSING_FIRST)),
        );
        
        self::assertSame([ 'sort' => [ [ ':fld:' => ['missing' => '_first', 'order' => 'desc'] ]]],$sort->build());
    }

    public function test_it_combines(): void
    {
        $a = Sorting::for(
            new Sort(':fld1:', SortOrder::DESCENDING),
            new Sort(':fld2:', SortOrder::DESCENDING),
        );
        $b = Sorting::for(
            new Sort(':fld3:', SortOrder::DESCENDING),
            new Sort(':fld4:', SortOrder::DESCENDING),
        );
        $c = Sorting::for(
            new Sort(':fld5:', SortOrder::DESCENDING),
            new Sort(':fld6:', SortOrder::for(SortOrder::DESCENDING)),
        );
        $d = Sorting::for(
            new Sort(':fld7:', SortOrder::for(SortOrder::DESCENDING, SortOrder::MISSING_FIRST)),
        );
        $e = Sorting::for();

        $result = $a->combine($b, $c, $d, $e);

        self::assertNotSame($a->build(), $result->build());
        self::assertSame([
            'sort' => [
                [ ':fld1:' => 'desc' ],
                [ ':fld2:' => 'desc' ],
                [ ':fld3:' => 'desc' ],
                [ ':fld4:' => 'desc' ],
                [ ':fld5:' => 'desc' ],
                [ ':fld6:' => ['missing' => '_last', 'order' => 'desc']],
                [ ':fld7:' => ['missing' => '_first', 'order' => 'desc']]
            ],
        ], $result->build());
    }
}
