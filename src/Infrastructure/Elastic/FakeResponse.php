<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\Elastic;

use Elastic\Elasticsearch\Response\Elasticsearch;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Webmozart\Assert\Assert;

class FakeResponse
{
    private int $statusCode;

    private $body;

    /**
     * @param resource $body
     */
    public function __construct(int $statusCode, $body)
   {
       Assert::resource($body);
       $this->statusCode = $statusCode;
       $this->body = $body;
   }

    public function toArray(): array
    {
        return [
            'status' => $this->statusCode,
            'transfer_stats' => ['total_time' => 100],
            'body' => $this->body,
            'effective_url' => 'localhost'
        ];
    }

    public function toResponse(): ResponseInterface
    {
        return new Response(
            $this->statusCode,
            [
                Elasticsearch::HEADER_CHECK => Elasticsearch::PRODUCT_NAME,
                'Content-Type' => 'application/json',
            ],
            $this->body
        );
    }
}
