<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\Elastic;

use Elasticsearch\ClientBuilder;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Facades\Log;

final class ElasticClientBuilder
{
    private const HOST_KEYS = ['host', 'port', 'scheme'];

    public static function fromConfig(Repository $config): ClientBuilder
    {
        $builder = ClientBuilder::create();

        $hostConnectionProperties = array_filter(
            $config->get('explorer.connection'),
            static fn ($key) => in_array($key, self::HOST_KEYS, true),
            ARRAY_FILTER_USE_KEY
        );

        $builder->setHosts([$hostConnectionProperties]);

        if ($config->has('explorer.additionalConnections')) {
            $builder->setHosts([$config->get('explorer.connection'), ...$config->get('explorer.additionalConnections')]);
        }
        if ($config->has('explorer.connection.selector')) {
            $builder->setSelector($config->get('explorer.connection.selector'));
        }

        if($config->has('explorer.connection.api')) {
            $builder->setApiKey(
                $config->get('explorer.connection.api.id'),
                $config->get('explorer.connection.api.key')
            );
        }

        if($config->has('explorer.connection.elasticCloudId')) {
            $builder->setElasticCloudId(
                $config->get('explorer.connection.elasticCloudId'),
            );
        }

        if($config->has('explorer.connection.auth')) {
            $builder->setBasicAuthentication(
                $config->get('explorer.connection.auth.username'),
                $config->get('explorer.connection.auth.password')
            );
        }

        if($config->has('explorer.connection.ssl.verify')) {
            $builder->setSSLVerification($config->get('explorer.connection.ssl.verify'));
        }

        if($config->has('explorer.connection.ssl.key')) {
            [$path, $password] = self::getPathAndPassword($config->get('explorer.connection.ssl.key'));
            $builder->setSSLKey($path, $password);
        }

        if($config->has('explorer.connection.ssl.cert')) {
            [$path, $password] = self::getPathAndPassword($config->get('explorer.connection.ssl.cert'));
            $builder->setSSLCert($path, $password);
        }

        if($config->get('explorer.logging', false) && $config->has('explorer.logger')) {
            $logger = $config->get('explorer.logger');

            if(is_string($logger)) {
                $logger = Log::channel($logger);
            }

            $builder->setLogger($logger);
        }

        return $builder;
    }

    /**
     * @param array|string $config
     */
    private static function getPathAndPassword(mixed $config): array
    {
        return is_array($config) ? $config : [$config, null];
    }
}
