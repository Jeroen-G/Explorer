<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit;

use Elasticsearch\Client;
use InvalidArgumentException;
use JeroenG\Explorer\Application\Finder;
use JeroenG\Explorer\Application\BuildCommand;
use JeroenG\Explorer\Domain\Syntax\Matching;
use JeroenG\Explorer\Domain\Syntax\Term;
use Mockery;
use PHPUnit\Framework\TestCase;

class FinderTest extends TestCase
{
    private const TEST_INDEX = 'test_index';

    public function test_it_needs_an_index_to_even_try_to_find_your_stuff(): void
    {
        $client = Mockery::mock(Client::class);

        $builder = new BuildCommand();

        $subject = new Finder($client, $builder);

        $this->expectException(InvalidArgumentException::class);
        $subject->find();
    }

    public function test_it_finds_all_items_if_no_queries_are_provided(): void
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
                ],
            ])
            ->andReturn([
                'hits' => [
                    'total' => '1',
                    'hits' => [
                        $this->hit()
                    ],
                ],
            ]);

        $builder = new BuildCommand();
        $builder->setIndex(self::TEST_INDEX);

        $subject = new Finder($client, $builder);
        $results = $subject->find();

        self::assertCount(1, $results);
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
                                ['match' => ['title' => 'Lorem Ipsum']],
                                ['term' => ['subtitle' => 'Dolor sit amet']]
                            ],
                            'should' => [
                                ['match' => ['text' => 'consectetur adipiscing elit']],
                            ],
                            'filter' => [
                                ['term' => ['published' => true]],
                            ],
                        ],
                    ],
                ],
            ])
            ->andReturn([
                'hits' => [
                    'total' => '2',
                    'hits' => [
                        $this->hit(),
                        $this->hit(),
                    ],
                ],
            ]);

        $builder = new BuildCommand();
        $builder->setIndex(self::TEST_INDEX);
        $builder->setMust([new Matching('title', 'Lorem Ipsum')]);
        $builder->setShould([new Matching('text', 'consectetur adipiscing elit')]);
        $builder->setFilter([new Term('published', true)]);
        $builder->setWhere(['subtitle' => 'Dolor sit amet']);

        $subject = new Finder($client, $builder);
        $results = $subject->find();

        self::assertCount(2, $results);
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
