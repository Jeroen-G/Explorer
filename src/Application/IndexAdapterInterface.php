<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Application;

interface IndexAdapterInterface
{
    public function update(string $index, string $id, array $data);
    public function delete(string $index, string $id);
    public function flush(string $index);
    public function search(SearchCommandInterface $command): Results;
}
