<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit;

use JeroenG\Explorer\Domain\Aggregations\TermsAggregation;
use JeroenG\Explorer\Domain\Query\Query;
use JeroenG\Explorer\Domain\Query\Rescoring;
use JeroenG\Explorer\Domain\Syntax\MatchAll;
use JeroenG\Explorer\Domain\Syntax\Sort;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
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

    public function test_it_builds_query_with_params(): void
    {
        $this->query->setParams(['track_total_hits' => true]);

        $result = $this->query->build();
        self::assertArrayHasKey('track_total_hits', $result);
        self::assertEquals(true, $result['track_total_hits']);
    }

    public function test_it_builds_query_with_rescoring(): void
    {
        $rescoring = new Rescoring();
        $rescoring->setQuery(new MatchAll());
        $this->query->addRescoring($rescoring);
        $this->query->addRescoring($rescoring);

        $result = $this->query->build();

        self::assertEquals([
            'query' => ['match_all' => (object)[]],
            'rescore' => [
                $rescoring->build(),
                $rescoring->build()
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
}
