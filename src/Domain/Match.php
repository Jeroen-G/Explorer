<?php

namespace JeroenG\Explorer\Domain;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Laravel\Scout\Builder;
use Laravel\Scout\Engines\Engine;
use Webmozart\Assert\Assert;

class Match implements SyntaxInterface
{
    private string $field;

    /** @var mixed */
    private $value;

    public function __construct(string $field, $value)
    {
        $this->field = $field;
        $this->value = $value;
    }

    public function build(): array
    {
        return ['match' => [$this->field => $this->value]];
    }
}
