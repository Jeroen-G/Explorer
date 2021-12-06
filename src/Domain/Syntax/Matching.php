<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

class Matching implements SyntaxInterface
{
    private string $field;

    /** @var mixed */
    private $value;

    /** @var mixed */
    private $fuzziness;

    /** @var string */
    private $analyzer;

    /** @var bool */
    private $autoGenerateSynonymsPhraseQuery;

    /** @var integer */
    private $maxExpansions;

    /** @var integer */
    private $prefixLength;

    /** @var bool */
    private $fuzzyTranspositions;

    /** @var string */
    private $fuzzyRewrite;

    /** @var bool */
    private $lenient;

    /** @var string */
    private $operator;

    /** @var mixed */
    private $minimumShouldMatch;

    /** @var string */
    private $zeroTermsQuery;

    /** @var float */
    private $boost;


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

    private function getAnalyzer(): ?string
    {
        return $this->analyzer;
    }

    public function setAutoGenerateSynonymsPhraseQuery(bool $value): void
    {
        $this->autoGenerateSynonymsPhraseQuery = $value;
    }

    private function getAutoGenerateSynonymsPhraseQuery(): ?bool
    {
        return $this->autoGenerateSynonymsPhraseQuery;
    }

    public function setFuzziness(string $value): void
    {
        $this->fuzziness = $value;
    }

    private function getFuzziness()
    {
        return $this->fuzziness;
    }

    public function setMaxExpansions(int $value): void
    {
        $this->maxExpansions = $value;
    }

    private function getMaxExpansions(): ?int
    {
        return $this->maxExpansions;
    }

    public function setPrefixLength(int $value): void
    {
        $this->prefixLength = $value;
    }

    private function getPrefixLength(): ?int
    {
        return $this->prefixLength;
    }

    public function setFuzzyTranspositions(bool $value): void
    {
        $this->fuzzyTranspositions = $value;
    }

    private function getFuzzyTranspositions(): ?bool
    {
        return $this->fuzzyTranspositions;
    }

    public function setFuzzyRewrite(string $value): void
    {
        $this->fuzzyRewrite = $value;
    }

    private function getFuzzyRewrite(): ?string
    {
        return $this->fuzzyRewrite;
    }

    public function setLenient(bool $value): void
    {
        $this->lenient = $value;
    }

    private function getLenient(): ?bool
    {
        return $this->lenient;
    }

    public function setOperator(string $value): void
    {
        $this->operator = $value;
    }

    private function getOperator(): ?string
    {
        return $this->operator;
    }

    public function setMinimumShouldMatch(string $value): void
    {
        $this->minimumShouldMatch = $value;
    }

    private function getMinimumShouldMatch()
    {
        return $this->minimumShouldMatch;
    }

    public function setZeroTermsQuery(string $value): void
    {
        $this->zeroTermsQuery = $value;
    }

    private function getZeroTermsQuery(): ?string
    {
        return $this->zeroTermsQuery;
    }

    public function setBoost(float $value): void
    {
        $this->boost = $value;
    }

    private function getBoost(): ?float
    {
        return $this->boost;
    }

    public function build(): array
    {
        $query = [
            'query' => $this->value,
        ];

        if (!empty($this->getAnalyzer())) {
            $query['analyzer'] = $this->getAnalyzer();
        }

        if (!empty($this->getAutoGenerateSynonymsPhraseQuery())) {
            $query['auto_generate_synonyms_phrase_query'] = $this->getAutoGenerateSynonymsPhraseQuery();
        }

        if (!empty($this->getFuzziness())) {
            $query['fuzziness'] = $this->getFuzziness();
        }

        if (!empty($this->getMaxExpansions())) {
            $query['max_expansions'] = $this->getMaxExpansions();
        }

        if (!empty($this->getPrefixLength())) {
            $query['prefix_length'] = $this->getPrefixLength();
        }

        if (!empty($this->getFuzzyTranspositions())) {
            $query['fuzzy_transpositions'] = $this->getFuzzyTranspositions();
        }

        if (!empty($this->getFuzzyRewrite())) {
            $query['fuzzy_rewrite'] = $this->getFuzzyRewrite();
        }

        if (!empty($this->getLenient())) {
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

        if (!empty($this->getBoost())) {
            $query['boost'] = $this->getBoost();
        }

        return ['match' => [ $this->field => $query ] ];
    }
}
