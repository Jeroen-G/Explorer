<?php

declare(strict_types=1);

include __DIR__ . '/../vendor/autoload.php';

DG\BypassFinals::allowPaths([
    '*/vendor/elasticsearch/*',
]);
DG\BypassFinals::enable(false);