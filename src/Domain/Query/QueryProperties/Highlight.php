<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Query\QueryProperties;

use Webmozart\Assert\Assert;

/**
 * Highlights the fragments of the given fields that match the query.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/highlighting.html
 */
final class Highlight implements QueryProperty, Combinable
{
    /**
     * @param array<string, array> $fields
     * @param array<string, mixed> $options
     */
    private function __construct(
        private array $fields,
        private array $options = [],
    ) {
    }

    public static function for(string ...$fields): self
    {
        Assert::notEmpty($fields, 'At least one field to highlight is required.');

        return new self(array_fill_keys($fields, []));
    }

    public function field(string $field, array $options = []): self
    {
        return new self(array_replace($this->fields, [$field => $options]), $this->options);
    }

    public function tags(string $preTag, string $postTag): self
    {
        return $this->option('pre_tags', [$preTag])->option('post_tags', [$postTag]);
    }

    public function fragmentSize(int $fragmentSize): self
    {
        Assert::natural($fragmentSize);

        return $this->option('fragment_size', $fragmentSize);
    }

    public function numberOfFragments(int $numberOfFragments): self
    {
        Assert::natural($numberOfFragments);

        return $this->option('number_of_fragments', $numberOfFragments);
    }

    public function type(string $type): self
    {
        Assert::stringNotEmpty($type);

        return $this->option('type', $type);
    }

    public function requireFieldMatch(bool $requireFieldMatch): self
    {
        return $this->option('require_field_match', $requireFieldMatch);
    }

    /**
     * Sets any other highlighting option that applies to all fields, such as `order` or `no_match_size`.
     */
    public function option(string $name, mixed $value): self
    {
        return new self($this->fields, array_replace($this->options, [$name => $value]));
    }

    public function combine(...$self): self
    {
        $fields = $this->fields;
        $options = $this->options;

        /** @var Highlight[] $self */
        foreach ($self as $highlight) {
            $fields = array_replace($fields, $highlight->fields);
            $options = array_replace($options, $highlight->options);
        }

        return new self($fields, $options);
    }

    public function build(): array
    {
        return [
            'highlight' => array_merge($this->options, [
                // Cast to objects so fields without options are sent as {} instead of [].
                'fields' => array_map(static fn (array $options) => (object) $options, $this->fields),
            ]),
        ];
    }
}
