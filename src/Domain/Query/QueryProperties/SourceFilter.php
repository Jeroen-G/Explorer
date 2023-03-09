<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Query\QueryProperties;

class SourceFilter implements QueryProperty
{
    /**
     * @param string[]|null $include
     * @param string[]|null $exclude
     */
    private function __construct(
        private ?array $include = null,
        private ?array $exclude = null,
    ) {}

    public static function empty(): self
    {
        return new self();
    }

    public function include(string ...$fields): self
    {
        $include = $this->include ?? [];
        array_push($include, ...$fields);

        return new self(
            include: $include,
            exclude: $this->exclude,
        );
    }

    public function exclude(string ...$fields): self
    {
        $exclude = $this->exclude ?? [];
        array_push($exclude, ...$fields);

        return new self(
            include: $this->include,
            exclude: $exclude,
        );
    }

    public function build(): array
    {
        if ($this->exclude === null && $this->include === null) {
            return [];
        }

        $source = [];
        if (!is_null($this->include)) {
            $source['include'] = $this->include;
        }
        if (!is_null($this->exclude)) {
            $source['exclude'] = $this->exclude;
        }

        return [
            '_source' => $source,
        ];
    }
}