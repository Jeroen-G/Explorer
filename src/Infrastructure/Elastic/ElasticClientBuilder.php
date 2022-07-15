<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\Elastic;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

final class ElasticClientBuilder
{
    public static function fromConfig(): Client
    {
        $builder = ClientBuilder::create();

        $builder->setHosts([config('explorer.connection')]);

        if (config()->has('explorer.additionalConnections')) {
            $builder->setHosts([config('explorer.connection'), ...config('explorer.additionalConnections')]);
        }
        if (config()->has('explorer.connection.selector')) {
            $builder->setSelector(config('explorer.connection.selector'));
        }

        if(config()->has('explorer.connection.api')) {
            $builder->setApiKey(
                config('explorer.connection.api.id'),
                config('explorer.connection.api.key')
            );
        }

        if(config()->has('explorer.connection.elasticCloudId')) {
            $builder->setElasticCloudId(
                config('explorer.connection.elasticCloudId'),
            );
        }

        if(config()->has('explorer.connection.auth')) {
            $builder->setBasicAuthentication(
                config('explorer.connection.auth.username'),
                config('explorer.connection.auth.password')
            );
        }

        if(config()->has('explorer.connection.ssl.verify')) {
            $builder->setSSLVerification(config('explorer.connection.ssl.verify'));
        }

        if(config()->has('explorer.connection.ssl.key')) {
            [$path, $password] = self::getPathAndPassword(config('explorer.connection.ssl.key'));
            $builder->setSSLKey($path, $password);
        }

        if(config()->has('explorer.connection.ssl.cert')) {
            [$path, $password] = self::getPathAndPassword(config('explorer.connection.ssl.cert'));
            $builder->setSSLCert($path, $password);
        }

        return $builder->build();
    }

    /**
     * @param array|string $config
     */
    private static function getPathAndPassword(mixed $config): array
    {
        return is_array($config) ? $config : [$config, null];
    }
}
