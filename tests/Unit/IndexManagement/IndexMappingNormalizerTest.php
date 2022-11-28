<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\IndexManagement;

use JeroenG\Explorer\Domain\IndexManagement\IndexMappingNormalizer;
use PHPUnit\Framework\TestCase;

final class IndexMappingNormalizerTest extends TestCase
{
    public function test_it_normalizes_mapping(): void
    {
        $mapping = [
            'fld' => [
                'type' => 'text',
                'other' => 'This is a test'
            ],
            'other' => 'integer',
            'object' => [
                'id' => 'keyword',
                'age' => [ 'type' => 'integer' ]
            ],
        ];

        $normalizer = new IndexMappingNormalizer();

        $normalizedMapping = $normalizer->normalize($mapping);

        self::assertNotNull($normalizedMapping);
        self::assertEquals($normalizedMapping['fld'], $normalizedMapping['fld']);
        self::assertEquals([ 'type' => 'integer' ], $normalizedMapping['other']);

        $expectedObject = [
            'type' => 'nested',
            'properties' => [
                'id' => [
                    'type' => 'keyword',
                ],
                'age' => [
                    'type' => 'integer'
                ]
            ]
        ];

        self::assertEquals($expectedObject, $normalizedMapping['object']);
    }
}
