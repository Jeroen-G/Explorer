<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit;

use JeroenG\Explorer\Domain\Query\Query;
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

    public function test_it_builds_query()
    {
        $result = $this->query->build();
        self::assertEquals([ 'query' => $this->syntax->build() ], $result);
    }

    public function test_it_builds_query_with_sort()
    {
        $sort = new Sort('field', Sort::DESCENDING);
        $this->query->setSort([$sort]);

        $result = $this->query->build();
        self::assertEquals([$sort->build()], $result['sort'] ?? null);
    }

    public function test_it_builds_query_with_pagination()
    {
        $this->query->setLimit(10);
        $this->query->setOffset(30);

        $result = $this->query->build();
        self::assertEquals(30, $result['from'] ?? null);
        self::assertEquals(10, $result['size'] ?? null);
    }

    public function test_it_needs_both_limit_and_offset_for_pagination()
    {
        $this->query->setLimit(10);

        $result = $this->query->build();
        self::assertArrayNotHasKey('size', $result);
        self::assertArrayNotHasKey('from', $result);


        $this->query->setLimit(null);
        $this->query->setOffset(null);
        self::assertArrayNotHasKey('size', $result);
        self::assertArrayNotHasKey('from', $result);
    }

    public function test_it_builds_query_with_fields()
    {
        $this->query->setFields(['field.one']);

        $result = $this->query->build();
        self::assertEquals(['field.one'], $result['fields'] ?? null);
    }
}
