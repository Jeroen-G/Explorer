<?php

declare(strict_types=1);

use JeroenG\Ontology\Application\ConfigurationConfigurator;

return static function (ConfigurationConfigurator $configurator): ConfigurationConfigurator {
    return $configurator
        ->setDomainLayers(['Domain'])
        ->setApplicationLayers(['Application'])
        ->setInfrastructureLayers(['Infrastructure'])
        ->setGenerators([
            JeroenG\Ontology\Domain\Generators\Bibliography::class,
        ])
        ->setValidators([
            JeroenG\Ontology\Domain\Validators\Hexagonal\Repository::class,
            JeroenG\Ontology\Domain\Validators\ValueObject\Immutable::class,
        ])
        ;
};
