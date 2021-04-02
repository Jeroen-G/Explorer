<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\Scout;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use JeroenG\Explorer\Application\IndexAdapterInterface;
use JeroenG\Explorer\Application\Results;
use Laravel\Scout\Builder;
use Laravel\Scout\Engines\Engine;
use Webmozart\Assert\Assert;

final class Debugger
{
    private array $query;

    public function __construct(array $query)
    {
        $this->query = $query;
    }

    public function array(): array
    {
        return $this->query;
    }

    public function json(): string
    {
        return json_encode(self::$lastQuery, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
    }
}
