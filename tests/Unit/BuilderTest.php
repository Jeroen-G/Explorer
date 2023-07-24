<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit;

use JeroenG\Explorer\Domain\Aggregations\TermsAggregation;
use JeroenG\Explorer\Domain\Query\QueryProperties\TrackTotalHits;
use JeroenG\Explorer\Domain\Syntax\Compound\BoolQuery;
use JeroenG\Explorer\Domain\Syntax\MultiMatch;
use JeroenG\Explorer\Domain\Syntax\Term;
use JeroenG\Explorer\Domain\Syntax\Terms;
use JeroenG\Explorer\Infrastructure\Scout\Builder;
use JeroenG\Explorer\Tests\Support\Models\TestModelWithAliased;
use PHPUnit\Framework\TestCase;

class BuilderTest extends TestCase
{
    public function test_it_can_add_to_builder_bool_query_properties(): void
    {
        $builder = new Builder(new TestModelWithAliased(), '');

        $this->assertEmpty($builder->should);
        $this->assertEmpty($builder->must);
        $this->assertEmpty($builder->filter);
        $this->assertEmpty($builder->fields);
        $this->assertEmpty($builder->queryProperties);

        $should = new Term('field', 'value');
        $must = new MultiMatch('search');
        $filter = new Terms('tags', ['a', 'b']);

        $builder->should($should);
        $builder->must($must);
        $builder->filter($filter);
        $builder->field('body')->field('field')->field('tags');
        $builder->property(TrackTotalHits::all());

        $this->assertContains($should, $builder->should);
        $this->assertContains($must, $builder->must);
        $this->assertContains($filter, $builder->filter);
        $this->assertCount(3, $builder->fields);
        $this->assertCount(1, $builder->queryProperties);
    }

    public function test_it_can_add_to_builder_aggregation_property(): void
    {
        $builder = new Builder(new TestModelWithAliased(), '');

        $this->assertEmpty($builder->aggregations);

        $aggregation = new TermsAggregation(':field:');
        $builder->aggregation('field', $aggregation);

        $this->assertContains($aggregation, $builder->aggregations);
    }

    public function test_it_can_add_to_builder_compound_property(): void
    {
        $builder = new Builder(new TestModelWithAliased(), '');

        $this->assertNull($builder->compound);

        $boolQuery = new BoolQuery();
        $builder->newCompound($boolQuery);

        $this->assertSame($boolQuery, $builder->compound);
    }
}
