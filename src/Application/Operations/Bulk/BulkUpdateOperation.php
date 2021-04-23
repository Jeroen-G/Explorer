<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Application\Operations\Bulk;

use JeroenG\Explorer\Application\BePrepared;
use JeroenG\Explorer\Application\Explored;

class BulkUpdateOperation implements BulkOperationInterface
{
    /** @var Explored [] */
    private array $models = [];

    public static function from(iterable $iter): self
    {
        $operation = new self();
        if (is_array($iter)) {
            $operation->models = $iter;
        } elseif ($iter instanceof \Traversable) {
            $operation->models = iterator_to_array($iter, false);
        } else {
            throw new \InvalidArgumentException("Given argument is not iterable. Got " . get_class($iter));
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

    private static function modelToBulkAction(Explored $model)
    {
        return [
            'index' => [
                '_index' => $model->searchableAs(),
                '_id' => $model->getScoutKey(),
            ]
        ];
    }

    private static function modelToData(Explored $model)
    {
        $searchable = $model->toSearchableArray();
        if ($model instanceof BePrepared) {
            $searchable = $model->prepare($searchable);
        }

        return $searchable;
    }
}
