<?php declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Domain\Syntax;

use Illuminate\Testing\Assert;
use JeroenG\Explorer\Domain\Syntax\SortOrder;
use PHPUnit\Framework\TestCase;

final class SortOrderTest extends TestCase
{    
    public function test_it_uses_default_missing_when_creating_sort_order(): void
    {
        $sort = SortOrder::for(SortOrder::DESCENDING);
        
        Assert::assertSame([
            'missing' => SortOrder::MISSING_LAST,
            'order' => SortOrder::DESCENDING
        ], $sort->build());
    }
    
    /**
     * @dataProvider provideSortOrderStrings
     */
    public function test_sort_order_can_be_created_from_sort_string(string $expectedResult, string $sortString): void
    {
        $subject = SortOrder::fromString($sortString);
        Assert::assertSame($expectedResult, $subject->build());
    }
    
    /**
     * @dataProvider provideMissingSortOrderStrings
     */
    public function test_sort_order_can_be_created_from_sort_string_and_missing(array $expectedResult, string $sortString, string $missing): void
    {
        $subject = SortOrder::for($sortString, $missing);
        Assert::assertSame($expectedResult, $subject->build());
    }
    
    public function provideSortOrderStrings(): iterable
    {
        yield 'asc' => ['asc', 'asc'];
        yield 'desc' => ['desc', 'desc'];
    }
    
    public function provideMissingSortOrderStrings(): iterable
    {
        yield 'asc order with _last missing' => [['missing' => '_last', 'order' => 'asc'], 'asc', '_last'];
        yield 'desc order with _last missing' => [['missing' => '_last', 'order' => 'desc'], 'desc', '_last'];
        yield 'asc order with _first missing' => [['missing' => '_first', 'order' => 'asc'], 'asc', '_first'];
        yield 'desc order with _first missing' => [['missing' => '_first', 'order' => 'desc'], 'desc', '_first'];
    }    
}
