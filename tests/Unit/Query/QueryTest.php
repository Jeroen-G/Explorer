<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Query;

use JeroenG\Explorer\Domain\Aggregations\TermsAggregation;
use JeroenG\Explorer\Domain\Query\Query;
use JeroenG\Explorer\Domain\Query\QueryProperties\Rescoring;
use JeroenG\Explorer\Domain\Query\QueryProperties\SourceFilter;
use JeroenG\Explorer\Domain\Query\QueryProperties\TrackTotalHits;
use JeroenG\Explorer\Domain\Syntax\MatchAll;
use JeroenG\Explorer\Domain\Syntax\Sort;
use JeroenG\Explorer\Domain\Syntax\SortOrder;
use JeroenG\Explorer\Domain\Syntax\Term;
use PHPUnit\Framework\TestCase;
use TypeError;

final class QueryTest extends TestCase
{
    private MatchAll $syntax;

    private Query $query;

    protected function setUp(): void
    {
        parent::setUp();

        $this->syntax = new MatchAll();
        $this->query = Query::with($this->syntax);
    }

    public function test_it_builds_query(): void
    {
        $result = $this->query->build();
        self::assertEquals([ 'query' => $this->syntax->build() ], $result);
        self::assertFalse($this->query->hasAggregations());
    }

    public function test_it_builds_query_with_sort(): void
    {
        $sort = new Sort('field', Sort::DESCENDING);
        $this->query->setSort([$sort]);

        $result = $this->query->build();
        self::assertEquals([$sort->build()], $result['sort'] ?? null);
    }
    
    public function test_it_builds_query_with_sort_order(): void
    {
        $sort = new Sort('field', SortOrder::for(SortOrder::DESCENDING, SortOrder::MISSING_FIRST));
        $this->query->setSort([$sort]);
        
        $result = $this->query->build();
        self::assertEquals([$sort->build()], $result['sort'] ?? null);
    }

    public function test_it_throws_on_invalid_sort_argument(): void
    {
        $this->expectException(TypeError::class);
        $this->query->setSort([new Term(':fld:', ':val:')]);
    }

    public function test_it_reset_sort(): void
    {
        $this->query->addQueryProperties(TrackTotalHits::all());
        $this->query->setSort([new Sort(':fld:')]);
        $this->query->setSort([]);

        $result = $this->query->build();

        self::assertArrayNotHasKey('sort', $result);
        self::assertArrayHasKey('track_total_hits', $result);
    }

    public function test_it_builds_query_with_pagination(): void
    {
        $this->query->setLimit(10);
        $this->query->setOffset(30);

        $result = $this->query->build();
        self::assertEquals(30, $result['from'] ?? null);
        self::assertEquals(10, $result['size'] ?? null);
    }

    public function test_it_needs_both_limit_and_offset_for_pagination(): void
    {
        $this->query->setLimit(10);

        $result = $this->query->build();
        self::assertArrayHasKey('size', $result);
        self::assertArrayNotHasKey('from', $result);

        $this->query->setLimit(null);
        $this->query->setOffset(null);
        $result = $this->query->build();
        self::assertArrayNotHasKey('size', $result);
        self::assertArrayNotHasKey('from', $result);
    }

    public function test_it_builds_query_with_limit_alone_for_custom_total_size(): void
    {
        $this->query->setLimit(10);

        $result = $this->query->build();
        self::assertArrayNotHasKey('from', $result);
        self::assertEquals(10, $result['size']);
    }

    public function test_it_builds_query_with_fields(): void
    {
        $this->query->setFields(['field.one']);

        $result = $this->query->build();
        self::assertEquals(['field.one'], $result['fields'] ?? null);
    }

    public function test_it_builds_query_with_rescoring(): void
    {
        $rescoring1 = new Rescoring();
        $rescoring1->setQuery(new MatchAll());
        $this->query->addRescoring($rescoring1);

        $rescoring2 = new Rescoring();
        $rescoring2->setQuery(new Term(':fld:'));
        $this->query->addRescoring($rescoring2);

        $result = $this->query->build();

        self::assertEquals([
            'query' => ['match_all' => (object)[]],
            'rescore' => [
                $rescoring1->build(),
                $rescoring2->build(),
            ]
        ], $result);
    }

    public function test_it_builds_query_with_aggregations(): void
    {
        $this->query->addAggregation(':name:', new TermsAggregation(':field:'));

        self::assertTrue($this->query->hasAggregations());

        self::assertEquals([
            'query' => ['match_all' => (object)[]],
            'aggs' => [
                ':name:' => [
                    'terms' => [
                        'field' => ':field:',
                        'size' => 10
                    ]
                ]
            ]
        ], $this->query->build());
    }

    public function test_it_builds_with_source_filter_query_property(): void
    {
        $include = [':test-1:', ':test-2:'];
        $exclude = [':test-3:'];
        $sourceFilter = SourceFilter::empty()->include(...$include)->exclude(...$exclude);

        $this->query->addQueryProperties($sourceFilter);

        self::assertEquals([
            'query' => ['match_all' => (object)[]],
            '_source' => [
                'include' => $include,
                'exclude' => $exclude,
            ]
        ], $this->query->build());
    }
}
