<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use JeroenG\Explorer\Application\BuildCommand;
use JeroenG\Explorer\Domain\Compound\BoolQuery;
use JeroenG\Explorer\Domain\Compound\CompoundSyntaxInterface;
use JeroenG\Explorer\Domain\Syntax\Sort;
use JeroenG\Explorer\Domain\Syntax\Term;
use Laravel\Scout\Builder;
use Mockery;
use PHPUnit\Framework\TestCase;

class BuildCommandTest extends TestCase
{
    private const TEST_INDEX = 'test_index';

    public function test_it_wraps_the_default_scout_builder(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->model = Mockery::mock(Model::class);

        $builder->model->expects('searchableAs')->andReturn(self::TEST_INDEX);

        $subject = BuildCommand::wrap($builder);

        self::assertSame(self::TEST_INDEX, $subject->getIndex());
    }

    public function test_it_can_get_the_index_from_the_scout_builder(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->model = Mockery::mock(Model::class);

        $builder->index = self::TEST_INDEX;

        $subject = BuildCommand::wrap($builder);

        self::assertSame(self::TEST_INDEX, $subject->getIndex());
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

        $subject = BuildCommand::wrap($builder);

        self::assertSame($expected, $subject->$getter());
    }

    /**
     * @dataProvider buildCommandProvider
     * @param string $method
     * @param mixed $expected
     */
    public function test_it_works_with_setters_and_getters(string $method, $expected): void
    {
        $command = new BuildCommand();

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
        $command = new BuildCommand();

        self::assertFalse($command->hasSort());

        $command->setSort(new Sort('id'));

        self::assertTrue($command->hasSort());
        self::assertSame(['id' => 'asc'], $command->getSort());

        $command->setSort(null);

        self::assertFalse($command->hasSort());
        self::assertSame([], $command->getSort());

        $command->setSort(new Sort('id', 'desc'));

        self::assertTrue($command->hasSort());
        self::assertSame(['id' => 'desc'], $command->getSort());

        $this->expectException(InvalidArgumentException::class);
        $command->setSort(new Sort('id', 'invalid'));
    }

    public function test_it_accepts_fields(): void
    {
        $input = ['specific.field', '*.length'];
        $command = new BuildCommand();

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
        $builder->sort = new Sort('id');

        $subject = BuildCommand::wrap($builder);

        self::assertSame(['id' => 'asc'], $subject->getSort());
    }

    public function test_it_can_get_the_fields_from_scout_builder(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->model = Mockery::mock(Model::class);
        $input = ['my.field', 'your.field'];

        $builder->index = self::TEST_INDEX;
        $builder->fields = $input;

        $subject = BuildCommand::wrap($builder);

        self::assertSame($input, $subject->getFields());
    }

    public function test_it_accepts_a_custom_compound(): void
    {
        $command = new BuildCommand();
        $compound = new BoolQuery();

        $command->setCompound($compound);

        self::assertSame($compound, $command->getCompound());
    }

    public function test_it_wraps_with_a_custom_compound(): void
    {
        $compound = Mockery::mock(CompoundSyntaxInterface::class);
        $builder = Mockery::mock(Builder::class);
        $builder->model = Mockery::mock(Model::class);
        $builder->index = self::TEST_INDEX;
        $builder->compound = $compound;

        $subject = BuildCommand::wrap($builder);

        self::assertSame($compound, $subject->getCompound());
        self::assertNotInstanceOf(BoolQuery::class, $subject->getCompound());
    }

    public function test_it_has_bool_query_as_default_compound(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->model = Mockery::mock(Model::class);
        $builder->index = self::TEST_INDEX;

        $subject = BuildCommand::wrap($builder);

        self::assertInstanceOf(BoolQuery::class, $subject->getCompound());
    }
}
