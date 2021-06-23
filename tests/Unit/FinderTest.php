<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit;

use Elasticsearch\Client;
use InvalidArgumentException;
use JeroenG\Explorer\Application\AggregationResult;
use JeroenG\Explorer\Application\SearchCommand;
use JeroenG\Explorer\Domain\Aggregations\TermsAggregation;
use JeroenG\Explorer\Domain\Query\Query;
use JeroenG\Explorer\Domain\Syntax\Compound\BoolQuery;
use JeroenG\Explorer\Domain\Syntax\Matching;
use JeroenG\Explorer\Domain\Syntax\Sort;
use JeroenG\Explorer\Domain\Syntax\Term;
use JeroenG\Explorer\Infrastructure\Elastic\Finder;
use JeroenG\Explorer\Infrastructure\Scout\ScoutSearchCommandBuilder;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class FinderTest extends MockeryTestCase
{
    private const TEST_INDEX = 'test_index';

    private const SEARCHABLE_FIELDS = [':field1:', ':field2:'];

    public function test_it_needs_an_index_to_even_try_to_find_your_stuff(): void
    {
        $client = Mockery::mock(Client::class);

        $builder = new SearchCommand();

        $subject = new Finder($client, $builder);

        $this->expectException(InvalidArgumentException::class);
        $subject->find();
    }

    public function test_it_finds_all_items_if_no_queries_are_provided(): void
    {
        $hit = $this->hit();

        $client = Mockery::mock(Client::class);
        $client->expects('search')
            ->with([
                'index' => self::TEST_INDEX,
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [],
                            'should' => [],
                            'filter' => [],
                        ],
                    ],
                ],
            ])
            ->andReturn([
                'hits' => [
                    'total' => ['value' => 1],
                    'hits' => [$hit],
                ],
            ]);

        $builder = new SearchCommand(self::TEST_INDEX, Query::with(new BoolQuery()));

        $subject = new Finder($client, $builder);
        $results = $subject->find();

        self::assertCount(1, $results);
        self::assertSame([$hit], $results->hits());
    }

    public function test_it_accepts_must_should_filter_and_where_queries(): void
    {
        $client = Mockery::mock(Client::class);
        $client->expects('search')
            ->with([
                'index' => self::TEST_INDEX,
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['match' => ['title' => [ 'query' => 'Lorem Ipsum', 'fuzziness' => 'auto']]],
                                ['multi_match' => ['query' => 'fuzzy search', 'fuzziness' => 'auto']],
                                ['term' => ['subtitle' => [ 'value' => 'Dolor sit amet', 'boost' => 1.0]]]
                            ],
                            'should' => [
                                ['match' => ['text' => [ 'query' => 'consectetur adipiscing elit', 'fuzziness' => 'auto']]],
                            ],
                            'filter' => [
                                ['term' => ['published' => [ 'value' => true, 'boost' => 1.0]]],
                            ],
                        ],
                    ],
                ],
            ])
            ->andReturn([
                'hits' => [
                    'total' => ['value' => 2],
                    'hits' => [
                        $this->hit(),
                        $this->hit(),
                    ],
                ],
            ]);

        $builder = new ScoutSearchCommandBuilder();
        $builder->setIndex(self::TEST_INDEX);
        $builder->setMust([new Matching('title', 'Lorem Ipsum')]);
        $builder->setShould([new Matching('text', 'consectetur adipiscing elit')]);
        $builder->setFilter([new Term('published', true)]);
        $builder->setWhere(['subtitle' => 'Dolor sit amet']);
        $builder->setQuery('fuzzy search');

        $subject = new Finder($client, $builder);
        $results = $subject->find();

        self::assertCount(2, $results);
    }

    public function test_it_accepts_a_query_for_paginated_search(): void
    {
        $client = Mockery::mock(Client::class);
        $client->expects('search')
            ->with([
                'index' => self::TEST_INDEX,
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [],
                            'should' => [],
                            'filter' => [],
                        ],
                    ],
                    'from' => 10,
                    'size' => 100,
                ],
            ])
            ->andReturn([
                'hits' => [
                    'total' => ['value' => 1],
                    'hits' => [$this->hit()],
                ],
            ]);

        $query = Query::with(new BoolQuery());
        $builder = new SearchCommand(self::TEST_INDEX);
        $builder->setQuery($query);
        $query->setOffset(10);
        $query->setLimit(100);

        $subject = new Finder($client, $builder);
        $results = $subject->find();

        self::assertCount(1, $results);
    }

    public function test_it_accepts_a_sortable_query(): void
    {
        $client = Mockery::mock(Client::class);
        $client->expects('search')
            ->with([
                'index' => self::TEST_INDEX,
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [],
                            'should' => [],
                            'filter' => [],
                        ],
                    ],
                    'sort' => [
                        ['id' => 'desc'],
                    ],
                ],
            ])
            ->andReturn([
                'hits' => [
                    'total' => ['value' => 1],
                    'hits' => [$this->hit()],
                ],
            ]);

        $query = Query::with(new BoolQuery());
        $builder = new SearchCommand(self::TEST_INDEX, $query);
        $query->setSort([new Sort('id', Sort::DESCENDING)]);

        $subject = new Finder($client, $builder);
        $results = $subject->find();

        self::assertCount(1, $results);
    }

    public function test_it_must_provide_offset_and_limit_for_pagination(): void
    {
        $client = Mockery::mock(Client::class);
        $client->expects('search')
            ->with([
                'index' => self::TEST_INDEX,
                'body' => [
                    'size' => 100,
                    'query' => [
                        'bool' => [
                            'must' => [],
                            'should' => [],
                            'filter' => [],
                        ],
                    ],
                ],
            ])
            ->andReturn([
                'hits' => [
                    'total' => ['value' => 1],
                    'hits' => [$this->hit()],
                ],
            ]);

        $query = Query::with(new BoolQuery());
        $builder = new SearchCommand(self::TEST_INDEX, $query);
        $builder->setIndex(self::TEST_INDEX);
        $query->setLimit(100);

        $subject = new Finder($client, $builder);
        $results = $subject->find();

        self::assertCount(1, $results);
    }

    public function test_it_builds_with_default_fields(): void
    {
        $client = Mockery::mock(Client::class);
        $client->expects('search')
            ->with([
                'index' => self::TEST_INDEX,
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['multi_match' => ['query' => 'fuzzy search', 'fields' => self::SEARCHABLE_FIELDS, 'fuzziness' => 'auto' ]],
                            ],
                            'should' => [],
                            'filter' => [],
                        ],
                    ],
                ],
            ])
            ->andReturn([
                'hits' => [
                    'total' => ['value' => 1],
                    'hits' => [$this->hit()],
                ],
            ]);

        $builder = new ScoutSearchCommandBuilder();
        $builder->setIndex(self::TEST_INDEX);
        $builder->setDefaultSearchFields(self::SEARCHABLE_FIELDS);
        $builder->setQuery('fuzzy search');

        $subject = new Finder($client, $builder);
        $results = $subject->find();

        self::assertCount(1, $results);
    }

    public function test_it_adds_fields_to_query(): void
    {
        $client = Mockery::mock(Client::class);
        $client->expects('search')
            ->with([
                'index' => self::TEST_INDEX,
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [],
                            'should' => [],
                            'filter' => [],
                        ],
                    ],
                    'fields' => ['*.length', 'specific.field']
                ],
            ])
            ->andReturn([
                'hits' => [
                    'total' => ['value' => 1],
                    'hits' => [$this->hit()],
                ],
            ]);

        $query = Query::with(new BoolQuery());
        $builder = new SearchCommand(self::TEST_INDEX, $query);
        $builder->setIndex(self::TEST_INDEX);
        $query->setFields(['*.length', 'specific.field']);

        $subject = new Finder($client, $builder);
        $results = $subject->find();

        self::assertCount(1, $results);
    }

    public function test_it_adds_aggregates(): void
    {
        $client = Mockery::mock(Client::class);
        $client->expects('search')
            ->with([
                'index' => self::TEST_INDEX,
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [],
                            'should' => [],
                            'filter' => [],
                        ],
                    ],
                    'aggs' => [
                        'specificAggregation' => ['terms' => ['field' => 'specificField']],
                        'anotherAggregation' => ['terms' => ['field' => 'anotherField']]
                    ],
                ],
            ])
            ->andReturn([
                'hits' => [
                    'total' => ['value' => 1],
                    'hits' => [$this->hit()],
                ],
                'aggregations' => [
                    'specificAggregation' => [
                        'buckets' => [
                            ['key' => 'myKey', 'doc_count' => 42]
                        ]
                    ],
                    'anotherAggregation' => [
                        'buckets' => [
                            ['key' => 'anotherKey', 'doc_count' => 6]
                        ]
                    ],
                ]
            ]);

        $query = Query::with(new BoolQuery());
        $query->addAggregation('specificAggregation', new TermsAggregation('specificField'));
        $query->addAggregation('anotherAggregation', new TermsAggregation('anotherField'));
        $builder = new SearchCommand(self::TEST_INDEX, $query);
        $builder->setIndex(self::TEST_INDEX);

        $subject = new Finder($client, $builder);
        $results = $subject->find();

        self::assertCount(2, $results->aggregations());

        $specificAggregation = $results->aggregations()[0];

        self::assertInstanceOf(AggregationResult::class, $specificAggregation);
        self::assertEquals('specificAggregation', $specificAggregation->name());
        self::assertCount(1, $specificAggregation->values());

        $specificAggregationValue = $specificAggregation->values()[0];

        self::assertEquals(42, $specificAggregationValue['doc_count']);
        self::assertEquals('myKey', $specificAggregationValue['key']);
    }

    private function hit(int $id = 1, float $score = 1.0): array
    {
        return [
            '_index' => self::TEST_INDEX,
            '_type' => 'default',
            '_id' => (string) $id,
            '_score' => $score,
            '_source' => [],
        ];
    }
}
