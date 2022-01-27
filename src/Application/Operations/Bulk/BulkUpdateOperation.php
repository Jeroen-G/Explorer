<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Application\Operations\Bulk;

use JeroenG\Explorer\Application\BePrepared;
use JeroenG\Explorer\Application\Explored;

final class BulkUpdateOperation implements BulkOperationInterface
{
    /** @var Explored[] */
    private array $models = [];

    private static string $indexName;

    public function __construct(string $indexName)
    {
        self::$indexName = $indexName;
    }

    public static function from(iterable $iterable, string $indexName): self
    {
        $operation = new self($indexName);

        if ($iterable instanceof \Traversable) {
            $operation->models = iterator_to_array($iterable);
        } else {
            $operation->models = $iterable;
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
            $payload[] = self::modelToData($model);
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

    private static function modelToData(Explored $model): array
    {
        $searchable = $model->toSearchableArray();
        if ($model instanceof BePrepared) {
            $searchable = $model->prepare($searchable);
        }
        $searchable['__class_name'] = \get_class($model);
        return $searchable;
    }
}
