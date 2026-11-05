<?php

declare(strict_types=1);

use PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\AssignmentInConditionSniff;
use PhpCsFixer\Fixer\Alias\MbStrFunctionsFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Fixer\FunctionNotation\ReturnTypeDeclarationFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\Strict\StrictComparisonFixer;
use PhpCsFixer\Fixer\Strict\StrictParamFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([__DIR__])
    ->withSkip(['node_modules/*', 'vendor/*', 'docs/*'])
    ->withPreparedSets(psr12: true, cleanCode: true)
    ->withRules([
        DeclareStrictTypesFixer::class,
        StrictComparisonFixer::class,
        StrictParamFixer::class,
        ReturnTypeDeclarationFixer::class,
        AssignmentInConditionSniff::class,
        MbStrFunctionsFixer::class,
        OrderedClassElementsFixer::class,
        ClassAttributesSeparationFixer::class,
    ]);
