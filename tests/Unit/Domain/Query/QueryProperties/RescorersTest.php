<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Domain\Query\QueryProperties;

use JeroenG\Explorer\Domain\Query\QueryProperties\Rescorers;
use JeroenG\Explorer\Domain\Query\QueryProperties\Rescoring;
use JeroenG\Explorer\Domain\Syntax\MatchAll;
use JeroenG\Explorer\Domain\Syntax\Term;
use PHPUnit\Framework\TestCase;

final class RescorersTest extends TestCase
{
    public function test_it_builds(): void
    {
        $sort = Rescorers::for(
            Rescoring::create(new Term(':fld:', ':val:')),
        );

        self::assertSame(['rescore' => [self::rescoreQueryPart(':fld:', ':val:')]], $sort->build());
    }

    public function test_it_combines(): void
    {
        $a = Rescorers::for(
            Rescoring::create(new Term(':fld1:', ':val1:')),
            Rescoring::create(new Term(':fld2:', ':val2:')),
        );
        $b = Rescorers::for(
            Rescoring::create(new Term(':fld3:', ':val3:')),
            Rescoring::create(new Term(':fld4:', ':val4:')),
        );
        $c = Rescorers::for(
            Rescoring::create(new Term(':fld5:', ':val5:')),
        );
        $d = Rescorers::for();

        $result = $a->combine($b, $c, $d);

        self::assertNotSame($a->build(), $result->build());
        self::assertSame([
            'rescore' => [
                self::rescoreQueryPart(':fld1:', ':val1:'),
                self::rescoreQueryPart(':fld2:', ':val2:'),
                self::rescoreQueryPart(':fld3:', ':val3:'),
                self::rescoreQueryPart(':fld4:', ':val4:'),
                self::rescoreQueryPart(':fld5:', ':val5:'),
            ],
        ], $result->build());
    }

    private static function rescoreQueryPart(string $fld, string $val): array
    {
        return [
            'window_size' => 10,
            'query' => [
                'score_mode' => 'total',
                'rescore_query' => [
                    'term' => [
                        $fld => [
                            'value' => $val,
                            'boost' => 1.0,
                        ]
                    ]
                ],
                'query_weight' => 1.0,
                'rescore_query_weight' => 1.0,
            ]
        ];
    }
}
