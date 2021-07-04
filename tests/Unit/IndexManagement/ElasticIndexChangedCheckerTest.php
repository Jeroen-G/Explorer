<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Tests\Unit\IndexManagement;

use JeroenG\Explorer\Application\IndexAdapterInterface;
use JeroenG\Explorer\Domain\IndexManagement\IndexConfiguration;
use JeroenG\Explorer\Infrastructure\IndexManagement\ElasticIndexChangedChecker;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class ElasticIndexChangedCheckerTest extends MockeryTestCase
{
    public const INDEX_NAME = 'test';

    /**
     * @var IndexAdapterInterface|\Mockery\MockInterface
     */
    private $adapter;

    private ElasticIndexChangedChecker $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adapter = Mockery::mock(IndexAdapterInterface::class);
        $this->subject = new ElasticIndexChangedChecker($this->adapter);
    }

    public function test_it_detects_changes_on_non_existing_index(): void
    {
        $targetConfig = IndexConfiguration::create(self::INDEX_NAME, [], []);

        $this->adapter->expects('getActualConfiguration')->with($targetConfig)->andReturnNull();

        $result = $this->subject->check($targetConfig);

        self::assertTrue($result);
    }

    /**
     * @dataProvider dataProviderWithSameIndices
     */
    public function test_it_works_for_same_indices(
        array $targetProperties,
        array $targetSettings,
        array $actualProperties,
        array $actualSettings
    ): void
    {
        $targetConfig = IndexConfiguration::create(self::INDEX_NAME, $targetProperties, $targetSettings);
        $actualConfig = IndexConfiguration::create(self::INDEX_NAME, $actualProperties, $actualSettings);

        $this->adapter->expects('getActualConfiguration')->with($targetConfig)->andReturn($actualConfig);

        $result = $this->subject->check($targetConfig);

        self::assertFalse($result);
    }

    public function dataProviderWithSameIndices(): \Generator
    {
        yield 'empty case' => [ [], [], [], [] ];

        yield 'full' => [
            ['id' => ['type' => 'keyword']],
            ['index' => ['max_ngram_diff' => '2'], 'tokenizer' => ['sample' => 'path_hierarchy'] ],
            ['id' => ['type' => 'keyword']],
            ['index' => ['max_ngram_diff' => '2'], 'tokenizer' => ['sample' => 'path_hierarchy'] ],
        ];

        yield 'ignores more properties on actual' => [
            ['id' => ['type' => 'keyword']],
            [],
            ['id' => ['type' => 'keyword'], 'name' => ['type' => 'text']],
            []
        ];

        yield 'ignores unknown settings' => [
            [],
            ['unknown' => true],
            [],
            ['unknown' => false],
        ];
    }

    /**
     * @dataProvider dataProviderWithDifferentIndices
     */
    public function test_it_detects_changes(
        array $targetProperties,
        array $targetSettings,
        array $actualProperties,
        array $actualSettings
    ): void
    {
        $targetConfig = IndexConfiguration::create(self::INDEX_NAME, $targetProperties, $targetSettings);
        $actualConfig = IndexConfiguration::create(self::INDEX_NAME, $actualProperties, $actualSettings);

        $this->adapter->expects('getActualConfiguration')->with($targetConfig)->andReturn($actualConfig);

        $result = $this->subject->check($targetConfig);

        self::assertTrue($result);
    }

    public function dataProviderWithDifferentIndices(): \Generator
    {
        yield 'empty different settings' => [
            [],
            ['index' => ['max_ngram_diff' => '2']],
            [],
            []
        ];

        yield 'empty different mapping' => [
            ['id' => ['type' => 'keyword']],
            [],
            [],
            []
        ];

        yield 'full different tokenizer' => [
            ['id' => ['type' => 'keyword']],
            ['index' => ['max_ngram_diff' => '2'], 'tokenizer' => ['sample' => 'simple'] ],
            ['id' => ['type' => 'keyword']],
            ['index' => ['max_ngram_diff' => '2'], 'tokenizer' => ['sample' => 'path_hierarchy'] ],
        ];

        yield 'different mapping field' => [
            ['id' => ['type' => 'keyword', 'fields' => ['text' => [ 'type' => 'text']]]],
            [],
            ['id' => ['type' => 'keyword']],
            [],
        ];

        yield 'different mapping type' => [
            ['id' => ['type' => 'integer']],
            [],
            ['id' => ['type' => 'keyword']],
            [],
        ];

        yield 'missing mapping' => [
            ['id' => ['type' => 'keyword'], 'name' => ['type' => 'keyword']],
            [],
            ['id' => ['type' => 'keyword']],
            [],
        ];
    }
}
