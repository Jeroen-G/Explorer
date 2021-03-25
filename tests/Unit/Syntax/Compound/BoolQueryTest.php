<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Syntax\Compound;

use InvalidArgumentException;
use JeroenG\Explorer\Domain\Syntax\Compound\BoolQuery;
use JeroenG\Explorer\Domain\Syntax\Matching;
use JeroenG\Explorer\Domain\Syntax\Term;
use JeroenG\Explorer\Tests\Support\QueryTypeProvider;
use JeroenG\Explorer\Tests\Support\SyntaxProvider;
use PHPUnit\Framework\TestCase;

class BoolQueryTest extends TestCase
{
    use SyntaxProvider;
    use QueryTypeProvider;

    public function test_it_can_build_an_empty_query(): void
    {
        $subject = new BoolQuery();

        $expected = [
            'bool' => [
                'must' => [],
                'should' => [],
                'filter' => [],
            ],
        ];

        $query = $subject->build();

        self::assertSame($expected, $query);
    }

    /**
     * @dataProvider queryTypeProvider
     * @param string $type
     */
    public function test_it_accepts_different_types_of_queries_by_method(string $type): void
    {
        $subject = new BoolQuery();
        $term = new Term('published', true);

        $subject->$type($term);

        $expected = [['term' => ['published' => true, 'boost' => 1.0]]];

        $query = $subject->build();

        self::assertSame($expected, $query['bool'][$type]);
    }

    /**
     * @dataProvider queryTypeProvider
     * @param string $type
     */
    public function test_it_accepts_different_types_of_queries_by_add(string $type): void
    {
        $subject = new BoolQuery();
        $term = new Term('published', true);

        $subject->add($type, $term);

        $expected = [['term' => ['published' => true, 'boost' => 1.0]]];

        $query = $subject->build();

        self::assertSame($expected, $query['bool'][$type]);
    }

    /**
     * @dataProvider syntaxProvider
     * @param string $className
     */
    public function test_it_accepts_different_types_of_syntax(string $className): void
    {
        $subject = new BoolQuery();
        $syntax = new $className('testcase');

        $subject->must($syntax);

        $expected = $subject->build();

        $query = $subject->build();

        self::assertSame($expected, $query);
        self::assertCount(1, $query['bool']['must']);
    }

    public function test_it_throws_an_error_when_adding_an_invalid_type(): void
    {
        $subject = new BoolQuery();
        $term = new Term('published', true);

        $this->expectException(InvalidArgumentException::class);
        $subject->add('invalid', $term);
    }

    public function test_it_allows_adding_many_queries_at_once(): void
    {
        $subject = new BoolQuery();

        $expected = [
            'bool' => [
                'must' => [],
                'should' => [
                    ['match' => ['title' => [ 'query' => 'Lorem Ipsum', 'fuzziness' => 'auto' ]]]
                ],
                'filter' => [
                    ['term' => ['published' => true, 'boost' => 1.0]],
                    ['term' => ['enabled' => true, 'boost' => 1.0]]
                ],
            ],
        ];

        $subject->should(new Matching('title', 'Lorem Ipsum'));
        $subject->addMany('filter', [
            new Term('published', true),
            new Term('enabled', true),
        ]);

        $query = $subject->build();

        self::assertSame($expected, $query);
    }

    public function test_it_fails_when_adding_many_but_one_is_invalid(): void
    {
        $subject = new BoolQuery();

        $this->expectException(InvalidArgumentException::class);
        $subject->addMany('filter', [
            new Term('published', true),
            'not a valid query',
        ]);
    }
}
