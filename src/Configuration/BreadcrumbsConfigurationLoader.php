<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Configuration;

use Jmf\Breadcrumbs\Exception\BreadcrumbConfigurationException;
use Jmf\Breadcrumbs\Exception\ConflictingBreadcrumbRouteDefinitionException;
use Jmf\Breadcrumbs\Exception\DuplicateBreadcrumbRouteDefinitionException;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use Webmozart\Assert\Assert;

/**
 * Assembles the final breadcrumbs map from per-route YAML files discovered under the configured
 * `paths` (filename without extension = route name) merged with inline `breadcrumbs` config.
 * A route name defined more than once is a configuration error.
 */
final readonly class BreadcrumbsConfigurationLoader
{
    /**
     * @param array<string, mixed> $config         resolved `jmf_breadcrumbs` config
     * @param string               $extensionAlias used to derive the default path
     *
     * @return array<string, array<string, mixed>> breadcrumb configs keyed by route name
     *
     * @throws BreadcrumbConfigurationException
     */
    public function load(
        array $config,
        ContainerBuilder $container,
        string $extensionAlias,
    ): array {
        $paths = $this->resolvePaths($config, $container, $extensionAlias);

        $this->registerResources($paths, $container);

        $fromPaths = $this->loadFromPaths($paths);

        Assert::keyExists($config, 'breadcrumbs');
        /** @var array<string, array<string, mixed>> $inline */
        $inline = $config['breadcrumbs'];
        Assert::isMap($inline);

        $this->detectDuplicates($fromPaths, $inline);

        return array_merge($fromPaths, $inline);
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return string[]
     */
    private function resolvePaths(
        array $config,
        ContainerBuilder $container,
        string $extensionAlias,
    ): array {
        Assert::keyExists($config, 'paths');
        $paths = $config['paths'];
        Assert::isArray($paths);

        if ([] === $paths) {
            $paths = ['%.kernel.config_dir%/packages/' . $extensionAlias];
        }

        $resolved = [];

        foreach ($paths as $path) {
            Assert::stringNotEmpty($path);
            $resolvedPath = $container->getParameterBag()->resolveValue($path);
            Assert::stringNotEmpty($resolvedPath);
            $resolved[] = $resolvedPath;
        }

        return $resolved;
    }

    /**
     * @param string[] $paths
     */
    private function registerResources(array $paths, ContainerBuilder $container): void
    {
        foreach ($paths as $path) {
            if (is_dir($path)) {
                $container->addResource(new DirectoryResource($path, '/\.yaml$/'));
            }
        }
    }

    /**
     * @param string[] $paths
     *
     * @return array<string, array<string, mixed>>
     *
     * @throws BreadcrumbConfigurationException
     */
    private function loadFromPaths(array $paths): array
    {
        $breadcrumbs = [];

        foreach ($paths as $path) {
            if (!is_dir($path)) {
                continue;
            }

            foreach ((new Finder())->files()->in($path)->name('*.yaml')->sortByName() as $file) {
                $routeName = substr($file->getFilename(), 0, -strlen('.yaml'));
                Assert::stringNotEmpty($routeName);

                if (isset($breadcrumbs[$routeName])) {
                    throw new DuplicateBreadcrumbRouteDefinitionException(
                        routeName: $routeName,
                    );
                }

                $parsed = Yaml::parseFile($file->getRealPath(), Yaml::PARSE_CONSTANT);
                Assert::isMap($parsed);

                $breadcrumbs[$routeName] = $parsed;
            }
        }

        return $breadcrumbs;
    }

    /**
     * @param array<string, mixed> $fromPaths
     * @param array<string, mixed> $inline
     *
     * @throws BreadcrumbConfigurationException
     */
    private function detectDuplicates(array $fromPaths, array $inline): void
    {
        $duplicates = array_intersect_key($fromPaths, $inline);

        if ([] !== $duplicates) {
            throw new ConflictingBreadcrumbRouteDefinitionException(
                routeNames: array_keys($duplicates),
            );
        }
    }
}
