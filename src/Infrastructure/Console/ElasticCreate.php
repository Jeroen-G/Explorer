<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\Console;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Console\Command;
use JeroenG\Explorer\Application\Explored;
use Symfony\Component\Console\Exception\RuntimeException;

class ElasticCreate extends Command
{
    protected $signature = 'elastic:create';

    protected $description = 'Create the Elastic indexes.';

    private Client $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()->build();
        parent::__construct();
    }

    public static function standardizeMapping(array $mappings)
    {
        $properties = [];

        foreach ($mappings as $field => $type) {
            $properties[$field] = self::standardizeElasticType($type);
        }

        return $properties;
    }

    public static function standardizeElasticType($type)
    {
        if (is_string($type)) {
            return ['type' => $type];
        }

        if (is_array($type)) {
            if (isset($type['type']) && isset($type['properties'])) {
                return [
                    'type' => $type['type'],
                    'properties' => self::standardizeMapping($type['properties'])
                ];
            }
            return [
                'type' => 'nested',
                'properties' => self::standardizeMapping($type)
            ];
        }

        $dump = var_export($type, true);
        throw new RuntimeException('Unable to determin mapping type: ' . $dump);
    }

    public function handle(): int
    {
        $config = config('explorer');

        if (!$config) {
            $this->warn('There are no indexes defined!');

            return 1;
        }

        foreach ($config['indexes'] as $key => $index) {
            [$name, $mappings] = $this->getConfiguration($key, $index);

            $properties = $this->getProperties($mappings);

            $this->createIndex($name, $properties);

            $this->info('Created index ' . $name);
        }

        return 0;
    }

    private function getConfiguration($name, $index): array
    {
        if (!is_string($index) || !class_exists($index)) {
            return [$name, $index['properties']];
        }

        $class = (new $index());

        if ($class instanceof Explored) {
            return [$class->searchableAs(), $class->mappableAs()];
        }

        if (is_int($name)) {
            throw new RuntimeException('Unable to decide on the index name');
        }

        return [$name, []];
    }

    private function getProperties(array $mappings): array
    {
        return self::standardizeMapping($mappings);
    }

    private function createIndex(string $name, array $properties): void
    {
        $this->client->indices()->create([
            'index' => $name,
            'body' => [
                'mappings' => [
                    'properties' => $properties
                ]
            ]
        ]);
    }
}
