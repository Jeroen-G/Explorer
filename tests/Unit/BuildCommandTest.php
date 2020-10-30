<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use JeroenG\Explorer\Application\BuildCommand;
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
}
