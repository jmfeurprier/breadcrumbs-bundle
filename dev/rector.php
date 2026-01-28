<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\Catch_\CatchExceptionNameMatchingTypeRector;
use Rector\Config\RectorConfig;
use Rector\Naming\Rector\Class_\RenamePropertyToMatchTypeRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;

$rootPath = realpath(__DIR__ . '/..') . '/';

return RectorConfig::configure()
    ->withCache($rootPath . 'var/cache/rector')
    ->withPaths(
        [
            $rootPath . 'config',
            $rootPath . 'src',
            $rootPath . 'tests',
        ],
    )
    ->withPhpSets()
    ->withSkip(
        [
            CatchExceptionNameMatchingTypeRector::class,
            PreferPHPUnitThisCallRector::class,
            RenamePropertyToMatchTypeRector::class,
        ],
    )
    ->withPreparedSets(
        deadCode:                 true,
        codeQuality:              true,
        codingStyle:              true,
        typeDeclarations:         true,
        typeDeclarationDocblocks: true,
        privatization:            true,
        naming:                   true,
        instanceOf:               true,
        earlyReturn:              true,
        carbon:                   true,
        rectorPreset:             true,
        phpunitCodeQuality:       true,
        doctrineCodeQuality:      true,
        symfonyCodeQuality:       true,
        symfonyConfigs:           true,
    )
;
