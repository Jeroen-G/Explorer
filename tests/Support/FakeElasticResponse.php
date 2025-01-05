<?php
declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Support;

use Elastic\Elasticsearch\Response\Elasticsearch;
use GuzzleHttp\Psr7\Response;

final class FakeElasticResponse extends Elasticsearch
{
    public static function array(array $data): self
    {
        $self = new self();
        $self->asArray = $data;
        return $self;
    }

    public static function bool(bool $bool): self
    {
        $self = new self();
        $self->response = new Response($bool ? 200 : 404);
        return $self;
    }
}
