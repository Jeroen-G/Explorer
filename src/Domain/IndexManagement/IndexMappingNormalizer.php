<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Domain\IndexManagement;

use RuntimeException;

final class IndexMappingNormalizer
{
    public function normalize(array $mappings): array
    {
        $properties = [];

        foreach ($mappings as $field => $type) {
            $properties[$field] = $this->normalizeElasticType($type);
        }

        return $properties;
    }

    private function normalizeElasticType($type): array
    {
        if (is_string($type)) {
            return ['type' => $type];
        }

        if (is_array($type)) {
            if (!isset($type['type'])) {
                $type = [
                    'type' => 'nested',
                    'properties' => $type
                ];
            }

            if (isset($type['type'], $type['properties'])) {
                return array_merge($type, [
                    'properties' => $this->normalize($type['properties']),
                ]);
            }

            return $type;
        }

        $dump = var_export($type, true);
        throw new RuntimeException('Unable to determine mapping type: ' . $dump);
    }
}