<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Analysis\Filter;

use Webmozart\Assert\Assert;

final class SynonymFilter implements FilterInterface
{
    private array $synonyms = [];

    public function getName(): string
    {
        return 'synonym';
    }

    public function setSynonyms(array $synonyms): void
    {
        Assert::allString($synonyms);
        $this->synonyms = $synonyms;
    }

    public function build(): array
    {
        return [
            'type' => 'synonym',
            'synonyms' => $this->synonyms,
        ];
    }
}
