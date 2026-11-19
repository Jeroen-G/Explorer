<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Domain\Query\QueryProperties;

use InvalidArgumentException;
use JeroenG\Explorer\Domain\Query\QueryProperties\Highlight;
use PHPUnit\Framework\TestCase;

final class HighlightTest extends TestCase
{
    public function test_it_highlights_fields_without_options_as_empty_objects(): void
    {
        $highlight = Highlight::for(':fld1:', ':fld2:');

        self::assertEquals(['highlight' => ['fields' => [':fld1:' => (object) [], ':fld2:' => (object) []]]], $highlight->build());
        self::assertSame('{"highlight":{"fields":{":fld1:":{},":fld2:":{}}}}', json_encode($highlight->build()));
    }

    public function test_it_requires_a_field(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('At least one field to highlight is required.');

        Highlight::for();
    }

    public function test_it_highlights_a_field_with_options(): void
    {
        $highlight = Highlight::for(':fld1:')->field(':fld2:', ['fragment_size' => 50, 'matched_fields' => [':fld2:', ':fld3:']]);

        self::assertSame(
            '{"highlight":{"fields":{":fld1:":{},":fld2:":{"fragment_size":50,"matched_fields":[":fld2:",":fld3:"]}}}}',
            json_encode($highlight->build())
        );
    }

    public function test_it_replaces_the_options_of_a_field_that_is_already_highlighted(): void
    {
        $highlight = Highlight::for(':fld1:', ':fld2:')
            ->field(':fld1:', ['type' => 'plain'])
            ->field(':fld1:', ['number_of_fragments' => 0]);

        self::assertSame(
            '{"highlight":{"fields":{":fld1:":{"number_of_fragments":0},":fld2:":{}}}}',
            json_encode($highlight->build())
        );
    }

    public function test_it_sets_options_for_all_fields(): void
    {
        $highlight = Highlight::for(':fld:')
            ->tags('<mark>', '</mark>')
            ->fragmentSize(150)
            ->numberOfFragments(3)
            ->type('unified')
            ->requireFieldMatch(false)
            ->option('order', 'score');

        self::assertSame(
            '{"highlight":{"pre_tags":["<mark>"],"post_tags":["<\/mark>"],"fragment_size":150,"number_of_fragments":3,'
            . '"type":"unified","require_field_match":false,"order":"score","fields":{":fld:":{}}}}',
            json_encode($highlight->build())
        );
    }

    public function test_it_replaces_an_option_that_is_already_set(): void
    {
        $highlight = Highlight::for(':fld:')->fragmentSize(150)->fragmentSize(0)->numberOfFragments(0);

        self::assertSame(
            '{"highlight":{"fragment_size":0,"number_of_fragments":0,"fields":{":fld:":{}}}}',
            json_encode($highlight->build())
        );
    }

    public function test_it_rejects_a_negative_fragment_size(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Highlight::for(':fld:')->fragmentSize(-1);
    }

    public function test_it_rejects_a_negative_number_of_fragments(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Highlight::for(':fld:')->numberOfFragments(-1);
    }

    public function test_it_rejects_an_empty_type(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Highlight::for(':fld:')->type('');
    }

    public function test_it_doesnt_mutate_state(): void
    {
        $highlight = Highlight::for(':fld:');
        $expected = $highlight->build();

        $highlight->field(':other:');
        $highlight->tags('<em>', '</em>');
        $highlight->option('order', 'score');

        self::assertEquals($expected, $highlight->build());
    }

    public function test_it_combines(): void
    {
        $a = Highlight::for(':fld1:', ':fld2:')->tags('<em>', '</em>')->fragmentSize(100);
        $b = Highlight::for(':fld2:')->field(':fld3:', ['number_of_fragments' => 0])->fragmentSize(50);
        $c = Highlight::for(':fld4:')->type('plain');

        $result = $a->combine($b, $c);

        self::assertNotEquals($a->build(), $result->build());
        self::assertSame(
            '{"highlight":{"pre_tags":["<em>"],"post_tags":["<\/em>"],"fragment_size":50,"type":"plain",'
            . '"fields":{":fld1:":{},":fld2:":{},":fld3:":{"number_of_fragments":0},":fld4:":{}}}}',
            json_encode($result->build())
        );
    }
}
