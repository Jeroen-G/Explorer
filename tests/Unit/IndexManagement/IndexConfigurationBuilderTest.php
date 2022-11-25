<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\IndexManagement;

use JeroenG\Explorer\Domain\IndexManagement\AliasedIndexConfiguration;
use JeroenG\Explorer\Domain\IndexManagement\DirectIndexConfiguration;
use JeroenG\Explorer\Domain\IndexManagement\IndexConfigurationBuilder;
use JeroenG\Explorer\Tests\Support\Models\TestModelWithoutSettings;
use PHPUnit\Framework\TestCase;

final class IndexConfigurationBuilderTest extends TestCase
{
    public function test_it_always_return_new_instance(): void
    {
        $original = IndexConfigurationBuilder::forExploredModel(new TestModelWithoutSettings());

        self::assertNotSame($original, $original->withProperties([]));
        self::assertNotSame($original, $original->withSettings([]));
        self::assertNotSame($original, $original->withModel(null));
        self::assertNotSame($original, $original->asAliased(false));
    }

    public function test_it_builds_direct_index_configuration_from_model(): void
    {
        $model = new TestModelWithoutSettings();
        $builder = IndexConfigurationBuilder::forExploredModel($model);

        $config = $builder->buildIndexConfiguration();
        self::assertInstanceOf(DirectIndexConfiguration::class, $config);
        self::assertSame($model->searchableAs(), $config->getWriteIndexName());
        self::assertSame($model->searchableAs(), $config->getReadIndexName());
    }

    public function test_it_builds_aliased_index_configuration(): void
    {
        $builder = IndexConfigurationBuilder::named('index')
            ->asAliased(false);

        $config = $builder->buildIndexConfiguration();

        self::assertInstanceOf(AliasedIndexConfiguration::class, $config);
        self::assertSame('index', $config->getReadIndexName());
        self::assertSame('index-write', $config->getWriteIndexName());
    }

    public function test_it_builds_index_configuration(): void
    {
        $builder = IndexConfigurationBuilder::named('index');
        $config = $builder->buildIndexConfiguration();

        self::assertInstanceOf(DirectIndexConfiguration::class, $config);
        self::assertSame('index', $config->getReadIndexName());
        self::assertSame('index', $config->getWriteIndexName());
    }

    public function test_it_sets_properties(): void
    {
        $properties = [ 'test'=> [ 'type' => 'text' ] ];
        $builder = IndexConfigurationBuilder::named('index')->withProperties($properties);
        $config = $builder->buildIndexConfiguration();

        self::assertEquals($properties, $config->getProperties());
    }

    public function test_it_normalizes_properties(): void
    {
        $normalizedProperties = [ 'test' => [ 'type' => 'text' ] ];
        $properties = [ 'test' => 'text' ];

        $builder = IndexConfigurationBuilder::named('index')->withProperties($properties);
        $config = $builder->buildIndexConfiguration();

        self::assertEquals($normalizedProperties, $config->getProperties());
    }

    public function test_it_sets_settings(): void
    {
        $settings = [ 'setting' => true ];

        $builder = IndexConfigurationBuilder::named('index')->withSettings($settings);
        $config = $builder->buildIndexConfiguration();

        self::assertSame($settings, $config->getSettings());
    }

    public function test_it_sets_model(): void
    {
        $builder = IndexConfigurationBuilder::named('index')->withModel(self::class);
        $config = $builder->buildIndexConfiguration();

        self::assertSame(self::class, $config->getModel());
    }
}
