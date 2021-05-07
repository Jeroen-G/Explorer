<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Application\Operations\Bulk;

use JeroenG\Explorer\Application\BePrepared;
use JeroenG\Explorer\Application\Explored;
use Webmozart\Assert\Assert;

class BulkUpdateOperation implements BulkOperationInterface
{
    /** @var Explored[] */
    private array $models = [];

    public static function from(iterable $iterable): self
    {
        $operation = new self();

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
            $payload[] = self::modelToBulkAction($model);
            $payload[] = self::modelToData($model);
        }
        return $payload;
    }

    private static function modelToBulkAction(Explored $model): array
    {
        return [
            'index' => [
                '_index' => $model->searchableAs(),
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

        return $searchable;
    }
}
