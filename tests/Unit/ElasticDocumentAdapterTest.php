<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Illuminate\Container\Container;
use JeroenG\Explorer\Application\Operations\Bulk\BulkUpdateOperation;
use JeroenG\Explorer\Infrastructure\Elastic\ElasticDocumentAdapter;
use JeroenG\Explorer\Tests\Support\ConfigRepository;
use JeroenG\Explorer\Tests\Support\Models\TestModelWithoutSettings;
use JeroenG\Explorer\Tests\Support\TestLogger;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class ElasticDocumentAdapterTest extends TestCase
{
    public function test_it_logs_bulk_operation_errors(): void
    {
        $configRepository = new ConfigRepository(['explorer' => ['bulk_refresh' => null]]);
        Container::getInstance()->instance('config', $configRepository);

        $logger = new TestLogger();

        // Mock Elasticsearch client
        $client = $this->createMock(Client::class);

        // Mock response with errors
        $mockResponse = $this->createMock(Elasticsearch::class);
        $bulkResponse = [
            'errors' => true,
            'items' => [
                [
                    'index' => [
                        '_index' => 'test_index',
                        '_id' => '1',
                        'status' => 400,
                        'error' => [
                            'type' => 'mapper_parsing_exception',
                            'reason' => 'failed to parse field [invalid_field]',
                            'caused_by' => [
                                'type' => 'number_format_exception',
                                'reason' => 'For input string: "invalid"'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $mockResponse->method('asArray')->willReturn($bulkResponse);
        $client->method('bulk')->willReturn($mockResponse);

        $adapter = new ElasticDocumentAdapter($client, $logger);

        // Create a mock bulk operation
        $bulkOperation = new BulkUpdateOperation('test_index', new NullLogger());
        $bulkOperation->add(new TestModelWithoutSettings());

        // Execute bulk operation
        $result = $adapter->bulk($bulkOperation);

        // Verify the response is returned
        self::assertEquals($bulkResponse, $result);

        // Check that error was logged
        self::assertTrue($logger->hasErrorRecords());
        $errorRecord = $logger->records[0];
        self::assertEquals('error', $errorRecord['level']);
        self::assertEquals('Elasticsearch bulk operation error', $errorRecord['message']);

        $context = $errorRecord['context'];
        self::assertEquals('index', $context['operation']);
        self::assertEquals('test_index', $context['index']);
        self::assertEquals('1', $context['id']);
        self::assertEquals(400, $context['status']);
        self::assertEquals('mapper_parsing_exception', $context['error_type']);
        self::assertEquals('failed to parse field [invalid_field]', $context['error_reason']);
        self::assertStringContainsString('mapper_parsing_exception', $context['error_chain']);
        self::assertStringContainsString('number_format_exception', $context['error_chain']);
        self::assertArrayHasKey('full_error', $context);
    }
}
