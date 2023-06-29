<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

class MultiMatch implements SyntaxInterface
{
    private $value;
    private ?float $boost = null;

    private ?array $fields;

    private $fuzziness;
    private $type;
    private $operator;
    private $minimumShouldMatch = null;

    public function __construct(string $value, ?array $fields = null, $fuzziness = 'auto')
    {
        $this->value = $value;
        $this->fields = $fields;
        $this->fuzziness = $fuzziness;
    }

    public function build(): array
    {
        $query = ['query' => $this->value ];

        if ($this->fields !== null) {
            $query['fields'] = $this->fields;
        }

        if (!empty($this->fuzziness)) {
            $query['fuzziness'] = $this->fuzziness;
        }

        if (!is_null($this->boost)) {
            $query['boost'] = $this->boost;
        }

        if (!is_null($this->operator)) {
            $query['operator'] = $this->operator;
        }

        if (!is_null($this->minimumShouldMatch)) {
            $query['minimum_should_match'] = $this->minimumShouldMatch;
        }

        if (!is_null($this->type)) {
            $query['type'] = $this->type;
        }

        return ['multi_match' => $query ];
    }

    public function setFuzziness($fuzziness): void
    {
        $this->fuzziness = $fuzziness;
    }

    public function setBoost(float $boost): void
    {
        $this->boost = $boost;
    }

    public function setOperator(string $operator): void
    {
        $this->operator = $operator;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function setMinimumShouldMatch(string $value): void
    {
        $this->minimumShouldMatch = $value;
    }
}
