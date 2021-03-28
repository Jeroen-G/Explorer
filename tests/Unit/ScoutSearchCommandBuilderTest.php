<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use JeroenG\Explorer\Application\SearchableFields;
use JeroenG\Explorer\Domain\Syntax\Compound\BoolQuery;
use JeroenG\Explorer\Domain\Syntax\Compound\QueryType;
use JeroenG\Explorer\Domain\Syntax\MultiMatch;
use JeroenG\Explorer\Domain\Syntax\Sort;
use JeroenG\Explorer\Domain\Syntax\SyntaxInterface;
use JeroenG\Explorer\Domain\Syntax\Term;
use JeroenG\Explorer\Infrastructure\Scout\ScoutSearchCommandBuilder;
use Laravel\Scout\Builder;
use Mockery;
use PHPUnit\Framework\TestCase;

class ScoutSearchCommandBuilderTest extends TestCase
{
    private const TEST_INDEX = 'test_index';

    private const TEST_SEARCHABLE_FIELDS = [':field1:', ':field2:'];

    public function test_it_wraps_the_default_scout_builder(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->model = Mockery::mock(Model::class);

        $builder->model->expects('searchableAs')->andReturn(self::TEST_INDEX);

        $subject = ScoutSearchCommandBuilder::wrap($builder);

        self::assertSame(self::TEST_INDEX, $subject->getIndex());
    }

    public function test_it_throws_on_null_index(): void
    {
        $builder = new ScoutSearchCommandBuilder();
        $this->expectException(InvalidArgumentException::class);
        $builder->getIndex();
    }

    public function test_it_can_get_the_index_from_the_scout_builder(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->model = Mockery::mock(Model::class);

        $builder->index = self::TEST_INDEX;

        $subject = ScoutSearchCommandBuilder::wrap($builder);

        self::assertSame(self::TEST_INDEX, $subject->getIndex());
    }

    public function test_it_gets_searchable_fields(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->model = Mockery::mock(Model::class, SearchableFields::class);

        $builder->index = self::TEST_INDEX;
        $builder->model->expects('getSearchableFields')->andReturn(self::TEST_SEARCHABLE_FIELDS);

        $subject = ScoutSearchCommandBuilder::wrap($builder);

        self::assertSame(self::TEST_SEARCHABLE_FIELDS, $subject->getDefaultSearchFields());
    }

    /**
     * @dataProvider buildCommandProvider
     * @param string $method
     * @param mixed $expected
     */
    public function test_it_sets_data_based_on_the_scout_builder(string $method, $expected): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->index = self::TEST_INDEX;

        $setter = mb_strtolower($method);
        $getter = "get{$method}";

        $builder->$setter = $expected;

        $subject = ScoutSearchCommandBuilder::wrap($builder);

        self::assertSame($expected, $subject->$getter());
    }

    /**
     * @dataProvider buildCommandProvider
     * @param string $method
     * @param mixed $expected
     */
    public function test_it_works_with_setters_and_getters(string $method, $expected): void
    {
        $command = new ScoutSearchCommandBuilder();

        $setter = "set{$method}";
        $getter = "get{$method}";

        self::assertEmpty($command->$getter());

        $command->$setter($expected);

        self::assertSame($expected, $command->$getter());
    }

    public function buildCommandProvider(): array
    {
        return [
            ['Must', [new Term('field', 'value')]],
            ['Should', [new Term('field', 'value')]],
            ['Filter', [new Term('field', 'value')]],
            ['Where', ['field' => 'value']],
            ['Query', 'Lorem Ipsum'],
        ];
    }

    public function test_it_can_set_the_sort_order(): void
    {
        $command = new ScoutSearchCommandBuilder();

        self::assertFalse($command->hasSort());

        $command->setSort([new Sort('id')]);

        self::assertTrue($command->hasSort());
        self::assertSame([['id' => 'asc']], $command->getSort());

        $command->setSort([]);

        self::assertFalse($command->hasSort());
        self::assertSame([], $command->getSort());

        $command->setSort([new Sort('id', 'desc')]);

        self::assertTrue($command->hasSort());
        self::assertSame([['id' => 'desc']], $command->getSort());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected one of: "asc", "desc". Got: "invalid"');

        $command->setSort([new Sort('id', 'invalid')]);
    }

    public function test_it_only_accepts_sort_classes(): void
    {
        $command = new ScoutSearchCommandBuilder();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected an instance of JeroenG\Explorer\Domain\Syntax\Sort. Got: string');

        $command->setSort(['not' => 'a class']);
    }

    public function test_it_accepts_fields(): void
    {
        $input = ['specific.field', '*.length'];
        $command = new ScoutSearchCommandBuilder();

        self::assertFalse($command->hasFields());
        self::assertSame([], $command->getFields());

        $command->setFields($input);

        self::assertTrue($command->hasFields());
        self::assertSame($input, $command->getFields());

        $command->setFields([]);
        self::assertFalse($command->hasFields());
        self::assertSame([], $command->getFields());
    }

    public function test_it_can_get_the_sorting_from_the_scout_builder(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->model = Mockery::mock(Model::class);

        $builder->index = self::TEST_INDEX;
        $builder->orders = [[ 'column' => 'id', 'direction' => 'asc']];

        $subject = ScoutSearchCommandBuilder::wrap($builder);

        self::assertSame([['id' => 'asc']], $subject->getSort());
    }

    public function test_it_can_get_the_fields_from_scout_builder(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->model = Mockery::mock(Model::class);
        $input = ['my.field', 'your.field'];

        $builder->index = self::TEST_INDEX;
        $builder->fields = $input;

        $subject = ScoutSearchCommandBuilder::wrap($builder);

        self::assertSame($input, $subject->getFields());
    }

    public function test_it_accepts_a_custom_compound(): void
    {
        $command = new ScoutSearchCommandBuilder();
        $compound = new BoolQuery();

        $command->setBoolQuery($compound);

        self::assertSame($compound, $command->getBoolQuery());
    }

    public function test_it_wraps_with_a_custom_compound(): void
    {
        $compound = Mockery::mock(BoolQuery::class);
        $builder = Mockery::mock(Builder::class);
        $builder->model = Mockery::mock(Model::class);
        $builder->index = self::TEST_INDEX;
        $builder->compound = $compound;

        $subject = ScoutSearchCommandBuilder::wrap($builder);

        self::assertSame($compound, $subject->getBoolQuery());
    }

    public function test_it_has_bool_query_as_default_compound(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->model = Mockery::mock(Model::class);
        $builder->index = self::TEST_INDEX;

        $subject = ScoutSearchCommandBuilder::wrap($builder);

        self::assertInstanceOf(BoolQuery::class, $subject->getBoolQuery());
    }

    public function test_it_builds_query(): void
    {
        $subject = new ScoutSearchCommandBuilder();

        $query = $subject->buildQuery();

        self::assertEquals(['query' => ['bool' => ['must' => [], 'should' => [], 'filter' => []]]], $query);
    }

    public function test_it_builds_query_with_input(): void
    {
        $subject = new ScoutSearchCommandBuilder();
        $sort = new Sort('sortfield', Sort::DESCENDING);
        $fields = ['test.field', 'other.field'];

        $subject->setOffset(10);
        $subject->setLimit(30);
        $subject->setSort([$sort]);
        $subject->setFields($fields);

        $query = $subject->buildQuery();

        $expectedQuery = [
            'query' => ['bool' => ['must' => [], 'should' => [], 'filter' => []]],
            'from' => 10,
            'size' => 30,
            'sort' => [$sort->build()],
            'fields' => $fields
        ];

        self::assertEquals($expectedQuery, $query);
    }

    public function test_it_adds_scout_properties_to_boolquery(): void
    {
        $boolQuery = Mockery::mock(BoolQuery::class);
        $subject = new ScoutSearchCommandBuilder();
        $term = new Term('field', 'value');
        $defaultFields = ['description', 'name'];
        $searchQuery = 'myQuery';
        $whereField = 'whereField';
        $whereValue = 'whereValue';
        $returnQuery = [ 'return' => 'query' ];

        $subject->setDefaultSearchFields($defaultFields);
        $subject->setQuery($searchQuery);
        $subject->setMust([$term]);
        $subject->setFilter([$term]);
        $subject->setShould([$term]);
        $subject->setBoolQuery($boolQuery);
        $subject->setWhere([ $whereField => $whereValue ]);

        $boolQuery->expects('clone')->andReturn($boolQuery);
        $boolQuery->expects('addMany')->with(QueryType::MUST, [$term]);
        $boolQuery->expects('addMany')->with(QueryType::SHOULD, [$term]);
        $boolQuery->expects('addMany')->with(QueryType::FILTER, [$term]);
        $boolQuery->expects('build')->andReturn($returnQuery);

        $boolQuery->expects('add')
            ->withArgs(function (string $type, SyntaxInterface $query) {
                return $type === 'must'
                    && $query instanceof MultiMatch;
            });

        $boolQuery->expects('add')
            ->withArgs(function (string $type, SyntaxInterface $query) {
                return $type === 'must'
                    && $query instanceof Term;
            });

        $query = $subject->buildQuery();

        self::assertSame([ 'query' => $returnQuery ], $query);
    }
}
