<?php
declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit;

use Elasticsearch\ClientBuilder;
use Elasticsearch\ConnectionPool\Selectors\StickyRoundRobinSelector;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use JeroenG\Explorer\Infrastructure\Elastic\ElasticClientBuilder;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class ElasticClientBuilderTest extends MockeryTestCase
{
    private const CLOUD_ID = 'staging:dXMtZWFzdC0xLmF3cy5mb3VuZC5pbyRjZWM2ZjI2MWE3NGJmMjRjZTMzYmI4ODExYjg0Mjk0ZiRjNmMyY2E2ZDA0MjI0OWFmMGNjN2Q3YTllOTYyNTc0Mw';

    private const CONNECTION = [ 'host' => 'example.com', 'port' => '9222', 'scheme' => 'https' ];

    /** @dataProvider provideClientConfigs */
    public function test_it_creates_client_with_config(array $config, ClientBuilder $clientBuilder): void
    {
        Container::getInstance()->instance('config', new Repository([ 'explorer' => $config ]));
        $expectedClient = $clientBuilder->build();

        $resultClient  = ElasticClientBuilder::fromConfig();

        self::assertEquals($expectedClient, $resultClient);
    }

    public function provideClientConfigs()
    {
        yield 'simple host' => [
            [
                'connection' => self::CONNECTION
            ],
            ClientBuilder::create()
                ->setHosts([self::CONNECTION])
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
                ->setHosts([self::CONNECTION])
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
                ->setHosts([self::CONNECTION])
                ->setApiKey('myId', 'myKey'),
        ];

         yield 'with selector' => [
            [
                'connection' => array_merge([
                    'selector' => StickyRoundRobinSelector::class
                ], self::CONNECTION)
            ],
            ClientBuilder::create()
                ->setHosts([self::CONNECTION])
                ->setSelector(StickyRoundRobinSelector::class),
        ];

        yield 'with ca verify disabled' => [
            [
                'connection' => array_merge([
                    'ssl' => ['verify' => false]
                ], self::CONNECTION)
            ],
            ClientBuilder::create()
                ->setHosts([self::CONNECTION])
                ->setSSLVerification(false),
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
                ->setHosts([self::CONNECTION])
                ->setSSLCert('path/to/cert.pem', 'passphrase')
                ->setSSLKey('path/to/key.pem', 'passphrase'),
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
    }
}
