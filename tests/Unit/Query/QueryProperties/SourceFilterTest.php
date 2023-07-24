<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Query\QueryProperties;

use JeroenG\Explorer\Domain\Query\QueryProperties\SourceFilter;
use PHPUnit\Framework\TestCase;

final class SourceFilterTest extends TestCase
{
    public function test_it_defaults_empty(): void
    {
        $subject = SourceFilter::empty();

        self::assertSame([], $subject->build());
    }

    public function test_it_adds_included_fields_in_output(): void
    {
        $subject = SourceFilter::empty()->include(':test-1:', ':test-2:');

        self::assertSame([ '_source' => [ 'include' => [ ':test-1:', ':test-2:' ] ] ], $subject->build());
    }

    public function test_it_adds_excluded_fields_in_output(): void
    {
        $subject = SourceFilter::empty()->exclude(':test-1:', ':test-2:');

        self::assertSame([ '_source' => [ 'exclude' => [ ':test-1:', ':test-2:' ] ] ], $subject->build());
    }

    public function test_it_adds_both_included_and_excluded_fields_in_output(): void
    {
        $subject = SourceFilter::empty()->exclude(':test-1:', ':test-2:')->include(':test-3:');

        self::assertSame([
            '_source' => [ 'include' => [ ':test-3:' ], 'exclude' => [ ':test-1:', ':test-2:' ] ]
        ], $subject->build());
    }

    public function test_it_doesnt_mutate_state(): void
    {
        $subject = SourceFilter::empty();

        self::assertNotSame($subject, $subject->include(':field:'));
        self::assertNotSame($subject->build(), $subject->include(':field:')->build());

        self::assertNotSame($subject, $subject->exclude(':field:'));
        self::assertNotSame($subject->build(), $subject->exclude(':field:')->build());
    }

    public function test_it_adds_fields(): void
    {
        $subject = SourceFilter::empty();

        self::assertSame(
            [ '_source' => [ 'include' => [ ':field-1:', ':field-2:' ] ] ],
            $subject->include(':field-1:')->include(':field-2:')->build()
        );

        self::assertSame(
            [ '_source' => [ 'exclude' => [ ':field-1:', ':field-2:' ] ] ],
            $subject->exclude(':field-1:')->exclude(':field-2:')->build()
        );
    }
}
