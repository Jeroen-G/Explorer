<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Application\Operations\Bulk;

use JeroenG\Explorer\Application\BePrepared;
use JeroenG\Explorer\Application\Explored;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class BulkUpdateOperation implements BulkOperationInterface
{
    /** @var Explored[] */
    private array $models = [];

    private static string $indexName;

    private ?LoggerInterface $logger;

    public function __construct(string $indexName, ?LoggerInterface $logger = null)
    {
        self::$indexName = $indexName;
        $this->logger = $logger;
    }

    public static function from(iterable $iterable, string $indexName, ?LoggerInterface $logger = null): self
    {
        $operation = new self($indexName, $logger);

        if (is_array($iterable)) {
            $operation->models = $iterable;
        } else {
            $operation->models = iterator_to_array($iterable);
        }

        return $operation;
    }

    public function add(Explored $model): void
    {
        $this->models[] = $model;
    }

    public function build(): array
    {
        $payload = [];
        foreach ($this->models as $model) {
            $payload[] = self::bulkActionSettings($model);
            $payload[] = $this->modelToData($model);
        }
        return $payload;
    }

    private static function bulkActionSettings(Explored $model): array
    {
        return [
            'index' => [
                '_index' => self::$indexName,
                '_id' => $model->getScoutKey(),
            ]
        ];
    }

    private function modelToData(Explored $model): array
    {
        try {
            $searchable = $model->toSearchableArray();
            if ($model instanceof BePrepared) {
                $searchable = $model->prepare($searchable);
            }

            return $searchable;
        } catch (\Throwable $e) {
            $this->logger?->error('Error in toSearchableArray() or prepare() method', [
                'model_class' => get_class($model),
                'model_key' => $model->getScoutKey(),
                'index' => self::$indexName,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return empty array to continue with other models
            return [];
        }
    }
}
