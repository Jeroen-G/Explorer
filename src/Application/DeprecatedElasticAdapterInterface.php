<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Application;

@trigger_error('The ElasticAdapter class is deprecated since 2.4.0 and will be removed in 3.0', \E_USER_DEPRECATED);

interface DeprecatedElasticAdapterInterface
{
    public function update(string $index, string $id, array $data);

    public function delete(string $index, string $id);

    public function flush(string $index);

    public function search(SearchCommandInterface $command): Results;

    public function bulk(BulkOperationInterface $command);
}
