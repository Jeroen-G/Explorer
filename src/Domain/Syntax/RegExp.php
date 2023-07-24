<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

class RegExp implements SyntaxInterface
{
    private string $field;
    
    private ?string $value;

    private string $flags;

    private bool $insensitive;

    public function __construct(string $field, string $value = null, string $flags = 'ALL', bool $insensitive = false)
    {
        $this->field = $field;
        $this->value = $value;
        $this->flags = $flags;
        $this->insensitive = $insensitive;
    }

    public function build(): array
    {
        return ['regexp' => [
            $this->field => [
                'value' => $this->value,
                'flags' => $this->flags,
                'case_insensitive' => $this->insensitive,
            ],
        ]];
    }
}
