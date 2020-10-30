<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Syntax;

use Webmozart\Assert\Assert;

class Range implements SyntaxInterface
{
    public const RELATIONS = ['gt', 'gte', 'lt', 'lte'];

    private string $field;

    private array $definitions;

    public function __construct(string $field, array $definitions)
    {
        $this->field = $field;
        $this->definitions = $definitions;
        $this->validateDefinitions($definitions);
    }

    public function build(): array
    {
        return ['range' => [$this->field => $this->definitions]];
    }

    private function validateDefinitions(array $definitions): void
    {
        foreach ($definitions as $key => $value) {
            Assert::inArray($key, self::RELATIONS);
            Assert::notEmpty($value);
        }
    }
}
