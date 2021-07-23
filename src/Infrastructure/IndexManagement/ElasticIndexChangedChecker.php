<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\IndexManagement;

use JeroenG\Explorer\Application\IndexAdapterInterface;
use JeroenG\Explorer\Application\IndexChangedCheckerInterface;
use JeroenG\Explorer\Domain\IndexManagement\IndexConfigurationInterface;

final class ElasticIndexChangedChecker implements IndexChangedCheckerInterface
{
    private IndexAdapterInterface $indexAdapter;

    public function __construct(IndexAdapterInterface $indexAdapter)
    {
        $this->indexAdapter = $indexAdapter;
    }

    public function check(IndexConfigurationInterface $targetConfig): bool
    {
        $actualConfig = $this->indexAdapter->getRemoteConfiguration($targetConfig);

        if (is_null($actualConfig)) {
            return true;
        }

        if ($this->settingsDiffer($targetConfig->getSettings(), $actualConfig->getSettings())) {
            return true;
        }
        if (self::arrayDiffer($targetConfig->getProperties(), $actualConfig->getProperties())) {
            return true;
        }

        return false;
    }

    private function settingsDiffer(array $targetSettings, array $actualSettings): bool
    {
        $settingsToCheck = [['analysis'], ['index', 'max_ngram_diff'], ['similarity'], ['tokenizer']];

        foreach ($settingsToCheck as $setting) {
            [$target, $actual] = self::getProperty($setting, $targetSettings, $actualSettings);

            if (self::propertyDiffer($target, $actual)) {
                return true;
            }
        }

        return false;
    }

    private static function propertyDiffer($target, $actual): bool
    {
        if (is_array($target) && is_array($actual)) {
            return self::arrayDiffer($target, $actual);
        }
        return $target !== $actual;
    }

    private static function getProperty(array $path, array $target, array $actual): array
    {
        foreach ($path as $key) {
            $target = $target[$key] ?? [];
            $actual = $actual[$key] ?? [];
        }

        return [$target, $actual];
    }

    private static function arrayDiffer(array $array1, array $array2): bool
    {
        if (!empty(array_diff_key($array1, $array2) || !empty(array_diff_key($array2, $array1)))) {
            return true;
        }

        foreach ($array1 as $key => $value) {
            if (!is_array($value) && (!array_key_exists($key, $array2) || $array2[$key] !== $value)) {
                return true;
            }

            if (is_array($value)) {
                if (!is_array($array2[$key])) {
                    return true;
                }

                if (!empty(array_diff_key($array2[$key], $value))) {
                    return true;
                }

                $subArrayDiffer = self::arrayDiffer($value, $array2[$key]);
                if ($subArrayDiffer) {
                    return true;
                }
            }
        }

        return false;
    }
}
