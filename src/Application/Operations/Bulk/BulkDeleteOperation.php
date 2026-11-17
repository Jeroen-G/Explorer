<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Application\Operations\Bulk;

use JeroenG\Explorer\Application\Explored;

final class BulkDeleteOperation implements BulkOperationInterface
{
    /** @var Explored[] */
    private array $models = [];

    public function __construct(private string $indexName)
    {
    }

    public static function from(iterable $iterable, string $indexName): self
    {
        $operation = new self($indexName);

        foreach ($iterable as $model) {
            $operation->add($model);
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
            $payload[] = [
                'delete' => [
                    '_index' => $this->indexName,
                    '_id' => $model->getScoutKey(),
                ],
            ];
        }
        return $payload;
    }
}
