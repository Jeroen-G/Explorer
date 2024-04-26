<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\Elastic;

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
}
