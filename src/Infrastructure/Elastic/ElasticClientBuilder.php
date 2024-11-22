<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\Elastic;

use Elastic\ElasticSearch\ClientBuilder;
use Elastic\Transport\NodePool\Resurrect\ElasticsearchResurrect;
use Elastic\Transport\NodePool\Selector\SelectorInterface;
use Elastic\Transport\NodePool\SimpleNodePool;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Facades\Log;

final class ElasticClientBuilder
{
    private const HOST_KEYS = ['host', 'port', 'scheme'];

    public static function fromConfig(Repository $config): \Elastic\Elasticsearch\ClientBuilder
    {
        $builder = \Elastic\Elasticsearch\ClientBuilder::create();

        $hostConnectionProperties = array_filter(
            $config->get('explorer.connection'),
            static fn ($key) => in_array($key, self::HOST_KEYS, true),
            ARRAY_FILTER_USE_KEY
        );

        if (count($hostConnectionProperties)) {
            // only call set hosts if we found hosts in the config
            $builder->setHosts([$hostConnectionProperties]);
        }

        if ($config->has('explorer.additionalConnections')) {
            $builder->setHosts([$config->get('explorer.connection'), ...$config->get('explorer.additionalConnections')]);
        }

        // untested
        if ($config->has('explorer.connection.selector')) {
            $selectorClass = $config->get('explorer.connection.selector');
            $selector = new $selectorClass();

            if (! $selector instanceof SelectorInterface) {
                throw new \InvalidArgumentException(get_class($selector) . " does not implement interface " . SelectorInterface::class);
            }

            $nodePool = new SimpleNodePool($selector, new ElasticsearchResurrect());
            $builder->setNodePool($nodePool);
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
