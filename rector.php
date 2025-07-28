<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\ClassMethod\LocallyCalledStaticMethodToNonStaticRector;
use Rector\CodeQuality\Rector\Expression\InlineIfToExplicitIfRector;
use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector;
use Rector\CodingStyle\Rector\Catch_\CatchExceptionNameMatchingTypeRector;
use Rector\CodingStyle\Rector\ClassMethod\NewlineBeforeNewAssignSetRector;
use Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector;
use Rector\Config\RectorConfig;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitSelfCallRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;
use Rector\TypeDeclaration\Rector\Class_\TypedPropertyFromCreateMockAssignRector;

return RectorConfig::configure()
    ->withImportNames(importShortClasses: false)
    ->withCache(__DIR__ . '/build/rector')
    ->withPaths([
        __DIR__ . '/bin',
        __DIR__ . '/packages/core',
        __DIR__ . '/packages/framework',
        __DIR__ . '/packages/phpstan',
        __DIR__ . '/packages/phpstan/tests',
        __DIR__ . '/packages/phpstan/config',
        __DIR__ . '/packages/phpstan/src',
        __DIR__ . '/packages/phpstan/tests',
        __DIR__ . '/packages/template/bin',
        __DIR__ . '/packages/template/config',
        __DIR__ . '/packages/template/public',
        __DIR__ . '/packages/template/src',
        __DIR__ . '/packages/template/tests',
        __DIR__ . '/packages/template/rector.php',
    ])
    ->withRootFiles() // must be called after `withPaths()`
    ->withPhpSets(php84: true)
    ->withAttributesSets(all: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        naming: false,
        instanceOf: true,
        earlyReturn: true,
        strictBooleans: false,
        carbon: false,
        rectorPreset: true,
        phpunitCodeQuality: true,
    )->withRules([
        PreferPHPUnitSelfCallRector::class,
    ])->withSkip([
        ClosureToArrowFunctionRector::class,
        FlipTypeControlToUseExclusiveTypeRector::class,
        PreferPHPUnitThisCallRector::class,
        InlineIfToExplicitIfRector::class,
        LocallyCalledStaticMethodToNonStaticRector::class,
        ExplicitBoolCompareRector::class,
        NewlineAfterStatementRector::class,
        NewlineBeforeNewAssignSetRector::class,
        CatchExceptionNameMatchingTypeRector::class,

        // Temporarily disabled due to buggy upstream implementation
        TypedPropertyFromCreateMockAssignRector::class,

        // Exclude test fixtures which may contain intentional nonconformant code
        __DIR__ . '/packages/core/tests/Fixtures',
        __DIR__ . '/packages/framework/tests/Fixtures',
        __DIR__ . '/packages/phpstan/tests/Fixtures',
    ]);
