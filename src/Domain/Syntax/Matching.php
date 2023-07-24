<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

class Matching implements SyntaxInterface
{
    private string $field;

    private mixed $value;

    private mixed $fuzziness;

    private ?string $analyzer = null;

    private ?bool $autoGenerateSynonymsPhraseQuery = null;

    private ?int $maxExpansions = null;

    private ?int $prefixLength = null;

    private ?bool $fuzzyTranspositions = null;

    private ?string $fuzzyRewrite = null;

    private ?bool $lenient = null;

    private ?string $operator = null;

    private mixed $minimumShouldMatch = null;

    private ?string $zeroTermsQuery = null;

    private ?float $boost = null;

    public function __construct(string $field, $value = null, $fuzziness = 'auto')
    {
        $this->field = $field;
        $this->value = $value;
        $this->fuzziness = $fuzziness;
    }

    public function setAnalyzer(string $value): void
    {
        $this->analyzer = $value;
    }

    public function setAutoGenerateSynonymsPhraseQuery(bool $value): void
    {
        $this->autoGenerateSynonymsPhraseQuery = $value;
    }

    public function setFuzziness(string $value): void
    {
        $this->fuzziness = $value;
    }

    public function setMaxExpansions(int $value): void
    {
        $this->maxExpansions = $value;
    }

    public function setPrefixLength(int $value): void
    {
        $this->prefixLength = $value;
    }

    public function setFuzzyTranspositions(bool $value): void
    {
        $this->fuzzyTranspositions = $value;
    }

    public function setFuzzyRewrite(string $value): void
    {
        $this->fuzzyRewrite = $value;
    }

    public function setLenient(bool $value): void
    {
        $this->lenient = $value;
    }

    public function setOperator(string $value): void
    {
        $this->operator = $value;
    }

    public function setMinimumShouldMatch(string $value): void
    {
        $this->minimumShouldMatch = $value;
    }

    public function setZeroTermsQuery(string $value): void
    {
        $this->zeroTermsQuery = $value;
    }

    public function setBoost(float $value): void
    {
        $this->boost = $value;
    }

    public function build(): array
    {
        $query = [
            'query' => $this->value,
        ];

        if (!empty($this->getAnalyzer())) {
            $query['analyzer'] = $this->getAnalyzer();
        }

        if (!is_null($this->getAutoGenerateSynonymsPhraseQuery())) {
            $query['auto_generate_synonyms_phrase_query'] = $this->getAutoGenerateSynonymsPhraseQuery();
        }

        if (!empty($this->getFuzziness())) {
            $query['fuzziness'] = $this->getFuzziness();
        }

        if (!is_null($this->getMaxExpansions())) {
            $query['max_expansions'] = $this->getMaxExpansions();
        }

        if (!is_null($this->getPrefixLength())) {
            $query['prefix_length'] = $this->getPrefixLength();
        }

        if (!is_null($this->getFuzzyTranspositions())) {
            $query['fuzzy_transpositions'] = $this->getFuzzyTranspositions();
        }

        if (!empty($this->getFuzzyRewrite())) {
            $query['fuzzy_rewrite'] = $this->getFuzzyRewrite();
        }

        if (!is_null($this->getLenient())) {
            $query['lenient'] = $this->getLenient();
        }

        if (!empty($this->getOperator())) {
            $query['operator'] = $this->getOperator();
        }

        if (!empty($this->getMinimumShouldMatch())) {
            $query['minimum_should_match'] = $this->getMinimumShouldMatch();
        }

        if (!empty($this->getZeroTermsQuery())) {
            $query['zero_terms_query'] = $this->getZeroTermsQuery();
        }

        if (!is_null($this->getBoost())) {
            $query['boost'] = $this->getBoost();
        }

        return ['match' => [ $this->field => $query ] ];
    }

    private function getAnalyzer(): ?string
    {
        return $this->analyzer;
    }

    private function getAutoGenerateSynonymsPhraseQuery(): ?bool
    {
        return $this->autoGenerateSynonymsPhraseQuery;
    }

    private function getFuzziness()
    {
        return $this->fuzziness;
    }

    private function getMaxExpansions(): ?int
    {
        return $this->maxExpansions;
    }

    private function getPrefixLength(): ?int
    {
        return $this->prefixLength;
    }

    private function getFuzzyTranspositions(): ?bool
    {
        return $this->fuzzyTranspositions;
    }

    private function getFuzzyRewrite(): ?string
    {
        return $this->fuzzyRewrite;
    }

    private function getLenient(): ?bool
    {
        return $this->lenient;
    }

    private function getOperator(): ?string
    {
        return $this->operator;
    }

    private function getMinimumShouldMatch()
    {
        return $this->minimumShouldMatch;
    }

    private function getZeroTermsQuery(): ?string
    {
        return $this->zeroTermsQuery;
    }

    private function getBoost(): ?float
    {
        return $this->boost;
    }
}
