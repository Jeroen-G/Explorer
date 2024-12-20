<?php
declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit;

use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Transport\NodePool\Resurrect\ElasticsearchResurrect;
use Elastic\Transport\NodePool\Selector\RoundRobin;
use Elastic\Transport\NodePool\SimpleNodePool;
use Illuminate\Container\Container;
use InvalidArgumentException;
use JeroenG\Explorer\Infrastructure\Elastic\ElasticClientBuilder;
use JeroenG\Explorer\Tests\Support\ConfigRepository;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Psr\Log\NullLogger;

final class ElasticClientBuilderTest extends MockeryTestCase
{
    private const CLOUD_ID = 'staging:dXMtZWFzdC0xLmF3cy5mb3VuZC5pbyRjZWM2ZjI2MWE3NGJmMjRjZTMzYmI4ODExYjg0Mjk0ZiRjNmMyY2E2ZDA0MjI0OWFmMGNjN2Q3YTllOTYyNTc0Mw';

    private const CONNECTION = [ 'host' => 'example.com', 'port' => '9222', 'scheme' => 'https' ];
    private const CONNECTION_STRING = 'https://example.com:9222';

    /** @dataProvider provideClientConfigs */
    public function test_it_creates_client_with_config(array $config, ClientBuilder $expectedBuilder): void
    {
        $configRepository = new ConfigRepository([ 'explorer' => $config ]);

        Container::getInstance()->instance('config', $configRepository);

        $resultBuilder  = ElasticClientBuilder::fromConfig($configRepository);

        self::assertEquals($expectedBuilder, $resultBuilder);
    }

    public function provideClientConfigs(): ?\Generator
    {
        yield 'simple host' => [
            [
                'connection' => self::CONNECTION
            ],
            ClientBuilder::create()
                ->setHosts([self::CONNECTION_STRING])
        ];

         yield 'elastic cloud id' => [
            [
                'connection' => [
                    'elasticCloudId' => self::CLOUD_ID
                ]
            ],
            ClientBuilder::create()
                ->setElasticCloudId(self::CLOUD_ID)
        ];

         yield 'with auth' => [
            [
                'connection' => array_merge([
                    'auth' => [
                        'username' => 'myName',
                        'password' => 'myPassword'
                    ]
                ], self::CONNECTION)
            ],
            ClientBuilder::create()
                ->setHosts([self::CONNECTION_STRING])
                ->setBasicAuthentication('myName', 'myPassword'),
        ];

         yield 'with api key' => [
            [
                'connection' => array_merge([
                    'api' => [
                        'id' => 'myId',
                        'key' => 'myKey'
                    ]
                ], self::CONNECTION)
            ],
            ClientBuilder::create()
                ->setHosts([self::CONNECTION_STRING])
                ->setApiKey('myId', 'myKey'),
        ];

         yield 'with selector' => [
            [
                'connection' => array_merge([
                    'selector' => RoundRobin::class
                ], self::CONNECTION)
            ],
            ClientBuilder::create()
                ->setHosts([self::CONNECTION_STRING])
                ->setNodePool(new SimpleNodePool(new RoundRobin(), new ElasticsearchResurrect())),
        ];

        yield 'with additional connections' => [
            [
                'connection' => self::CONNECTION,
                'additionalConnections' => [
                    self::CONNECTION,
                    self::CONNECTION,
                ]
            ],
            ClientBuilder::create()
                ->setHosts([self::CONNECTION, self::CONNECTION, self::CONNECTION]),
        ];

        yield 'with ca verify disabled' => [
            [
                'connection' => array_merge([
                    'ssl' => ['verify' => false]
                ], self::CONNECTION)
            ],
            ClientBuilder::create()
                ->setHosts([self::CONNECTION_STRING])
                ->setSSLVerification(false),
        ];

        yield 'with ca verify enabled' => [
            [
                'connection' => array_merge([
                    'ssl' => ['verify' => true]
                ], self::CONNECTION)
            ],
            ClientBuilder::create()
                ->setHosts([self::CONNECTION_STRING])
                ->setSSLVerification(),
        ];

        yield 'with key and cert' => [
            [
                'connection' => array_merge([
                    'ssl' => [
                        'cert' => ['path/to/cert.pem', 'passphrase'],
                        'key' => ['path/to/key.pem', 'passphrase'],
                    ]
                ], self::CONNECTION)
            ],
            ClientBuilder::create()
                ->setHosts([self::CONNECTION_STRING])
                ->setSSLCert('path/to/cert.pem', 'passphrase')
                ->setSSLKey('path/to/key.pem', 'passphrase'),
        ];

        yield 'with key and cert without passphrase' => [
            [
                'connection' => array_merge([
                    'ssl' => [
                        'cert' => 'path/to/cert.pem',
                        'key' => 'path/to/key.pem',
                    ]
                ], self::CONNECTION)
            ],
            ClientBuilder::create()
                ->setHosts([self::CONNECTION_STRING])
                ->setSSLCert('path/to/cert.pem')
                ->setSSLKey('path/to/key.pem'),
        ];

        yield 'with logging' => [
            [
                'logging' => true,
                'logger' => new NullLogger(),
                'connection' => self::CONNECTION,
            ],
            ClientBuilder::create()
                ->setHosts([self::CONNECTION_STRING])
                ->setLogger(new NullLogger()),
        ];

        yield 'without logging' => [
            [
                'logging' => false,
                'logger' => new NullLogger(),
                'connection' => self::CONNECTION,
            ],
            ClientBuilder::create()
                ->setHosts([self::CONNECTION_STRING]),
        ];

        yield 'without logger' => [
            [
                'logger' => new NullLogger(),
                'connection' => self::CONNECTION,
            ],
            ClientBuilder::create()
                ->setHosts([self::CONNECTION_STRING]),
        ];
    }

    public function testThrowsOnInvalidSelector(): void
    {
        $configRepository = new ConfigRepository([ 'explorer' => [ 'connection' => [ 'selector' => self::class ] ] ]);

        Container::getInstance()->instance('config', $configRepository);

        $this->expectException(InvalidArgumentException::class);
        ElasticClientBuilder::fromConfig($configRepository);
    }
}
