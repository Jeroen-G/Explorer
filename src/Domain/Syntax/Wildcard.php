<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

class Wildcard implements SyntaxInterface
{
    private string $field;

    private string $value;

    private float $boost;

    private bool $caseInsensitive = false;

    private ?string $rewrite = null;

    public function __construct(
        string $field,
        string $value,
        float $boost = 1.0
    ) {
        $this->field = $field;
        $this->value = $value;
        $this->boost = $boost;
    }

    public function setCaseInsensitive(bool $value): void
    {
        $this->caseInsensitive = $value;
    }

    public function setRewrite(string $value): void
    {
        $this->rewrite = $value;
    }

    public function build(): array
    {
        $query = [
            'value' => $this->value,
            'boost' => $this->boost,
            'case_insensitive' => $this->getCaseInsensitive(),
        ];

        if (!empty($this->getRewrite())) {
            $query['rewrite'] = $this->getRewrite();
        }

        return ['wildcard' => [ $this->field => $query ] ];
    }

    private function getCaseInsensitive(): bool
    {
        return $this->caseInsensitive;
    }

    private function getRewrite(): ?string
    {
        return $this->rewrite;
    }

}
