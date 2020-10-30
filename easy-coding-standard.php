<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Alias\MbStrFunctionsFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Fixer\FunctionNotation\ReturnTypeDeclarationFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\Strict\StrictComparisonFixer;
use PhpCsFixer\Fixer\Strict\StrictParamFixer;
use SlevomatCodingStandard\Sniffs\ControlStructures\AssignmentInConditionSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(DeclareStrictTypesFixer::class);

    $services->set(StrictComparisonFixer::class);

    $services->set(StrictParamFixer::class);

    $services->set(ReturnTypeDeclarationFixer::class);

    $services->set(AssignmentInConditionSniff::class);

    $services->set(MbStrFunctionsFixer::class);

    $services->set(OrderedClassElementsFixer::class);

    $services->set(ClassAttributesSeparationFixer::class);

    $parameters = $containerConfigurator->parameters();

    $parameters->set('sets', ['clean-code', 'psr12']);

    $parameters->set('exclude_files', ['node_modules/*', 'vendor/*', 'docs/*']);
};
