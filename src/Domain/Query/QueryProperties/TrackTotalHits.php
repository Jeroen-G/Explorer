<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\Query\QueryProperties;

class TrackTotalHits implements QueryProperty
{
    /** @param int|bool $trackTotalHits */
    private function __construct(
        private mixed $trackTotalHits,
    ) {}

    public static function all(): self
    {
        return new self(true);
    }

    public static function none(): self
    {
        return new self(false);
    }

    public static function count(int $count): self
    {
        return new self($count);
    }

    public function build(): array
    {
        return [
            'track_total_hits' => $this->trackTotalHits,
        ];
    }
}