<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\Syntax;

use JeroenG\Explorer\Domain\Syntax\Matching;
use PHPUnit\Framework\TestCase;

class MatchingTest extends TestCase
{
    public function test_it_builds_the_right_query(): void
    {
        $expectation = ['match' => ['test' => ['query' => 'value', 'fuzziness' => 'auto']]];
        $subject = new Matching('test', 'value');

        $result = $subject->build();

        self::assertEquals($expectation, $result);
    }

    public function test_it_builds_with_fuzziness(): void
    {
        $expectation = ['match' => ['test' => ['query' => 'value', 'fuzziness' => 2]]];
        $subject = new Matching('test', 'value', 2);

        $result = $subject->build();

        self::assertEquals($expectation, $result);
    }

    public function test_it_builds_with_boost(): void
    {
        $expectation = ['match' => ['test' => ['query' => 'value', 'fuzziness' => 'auto', 'boost' => 2]]];
        $subject = new Matching('test', 'value');
        $subject->setBoost(2);

        $result = $subject->build();

        self::assertEquals($expectation, $result);
    }

    public function test_it_builds_with_fuzziness_function(): void
    {
        $expectation = ['match' => ['test' => ['query' => 'value', 'fuzziness' => '2']]];
        $subject = new Matching('test', 'value');
        $subject->setFuzziness('2');

        $result = $subject->build();

        self::assertEquals($expectation, $result);
    }

    public function test_it_builds_without_fuzziness(): void
    {
        $expectation = ['match' => ['test' => ['query' => 'value']]];
        $subject = new Matching('test', 'value', null);

        $result = $subject->build();

        self::assertEquals($expectation, $result);
    }

    public function test_it_builds_with_analyzer(): void
    {
        $expectation = ['match' => ['test' => ['query' => 'value', 'fuzziness' => 'auto', 'analyzer' => 'whitespace']]];
        $subject = new Matching('test', 'value');
        $subject->setAnalyzer('whitespace');

        $result = $subject->build();

        self::assertEquals($expectation, $result);
    }

    public function test_it_builds_with_auto_generate_synonyms_phrase_query(): void
    {
        $expectation = ['match' => ['test' => ['query' => 'value', 'fuzziness' => 'auto', 'auto_generate_synonyms_phrase_query' => true]]];
        $subject = new Matching('test', 'value');
        $subject->setAutoGenerateSynonymsPhraseQuery(true);

        $result = $subject->build();

        self::assertEquals($expectation, $result);
    }

    public function test_it_builds_with_max_expansions(): void
    {
        $expectation = ['match' => ['test' => ['query' => 'value', 'fuzziness' => 'auto', 'max_expansions' => '100']]];
        $subject = new Matching('test', 'value');
        $subject->setMaxExpansions(100);

        $result = $subject->build();

        self::assertEquals($expectation, $result);
    }

    public function test_it_builds_with_prefix_length(): void
    {
        $expectation = ['match' => ['test' => ['query' => 'value', 'fuzziness' => 'auto', 'prefix_length' => 3]]];
        $subject = new Matching('test', 'value');
        $subject->setPrefixLength(3);

        $result = $subject->build();

        self::assertEquals($expectation, $result);
    }

    public function test_it_builds_with_fuzzy_transpositions(): void
    {
        $expectation = ['match' => ['test' => ['query' => 'value', 'fuzziness' => 'auto', 'fuzzy_transpositions' => true]]];
        $subject = new Matching('test', 'value');
        $subject->setFuzzyTranspositions(true);

        $result = $subject->build();

        self::assertEquals($expectation, $result);
    }

    public function test_it_builds_with_fuzzy_rewrite(): void
    {
        $expectation = ['match' => ['test' => ['query' => 'value', 'fuzziness' => 'auto', 'fuzzy_rewrite' => 'constant_score']]];
        $subject = new Matching('test', 'value');
        $subject->setFuzzyRewrite('constant_score');

        $result = $subject->build();

        self::assertEquals($expectation, $result);
    }

    public function test_it_builds_with_lenient(): void
    {
        $expectation = ['match' => ['test' => ['query' => 'value', 'fuzziness' => 'auto', 'lenient' => true]]];
        $subject = new Matching('test', 'value');
        $subject->setLenient(true);

        $result = $subject->build();

        self::assertEquals($expectation, $result);
    }

    public function test_it_builds_with_operator(): void
    {
        $expectation = ['match' => ['test' => ['query' => 'value', 'fuzziness' => 'auto', 'operator' => 'AND']]];
        $subject = new Matching('test', 'value');
        $subject->setOperator('AND');

        $result = $subject->build();

        self::assertEquals($expectation, $result);
    }

    public function test_it_builds_with_minimum_should_match(): void
    {
        $expectation = ['match' => ['test' => ['query' => 'value', 'fuzziness' => 'auto', 'minimum_should_match' => '50%']]];
        $subject = new Matching('test', 'value');
        $subject->setMinimumShouldMatch('50%');

        $result = $subject->build();

        self::assertEquals($expectation, $result);
    }

    public function test_it_builds_with_zero_terms_query(): void
    {
        $expectation = ['match' => ['test' => ['query' => 'value', 'fuzziness' => 'auto', 'zero_terms_query' => 'all']]];
        $subject = new Matching('test', 'value');
        $subject->setZeroTermsQuery('all');

        $result = $subject->build();

        self::assertEquals($expectation, $result);
    }
}
