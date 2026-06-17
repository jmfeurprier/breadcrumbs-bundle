<?php

declare(strict_types=1);

use Jmf\Breadcrumbs\Resolution\CurrentBreadcrumbNotFoundBehavior;
use Jmf\Breadcrumbs\Twig\BreadcrumbsExtension;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;

return static function (DefinitionConfigurator $definitionConfigurator): void {
    $definitionConfigurator->rootNode()
        ->children()
            ->arrayNode('paths')
                ->info('Directories of per-route breadcrumb files; filename (without .yaml) = route name. Defaults to config/packages/jmf_breadcrumbs/.')
                ->defaultValue([])
                ->scalarPrototype()
                    ->cannotBeEmpty()
                ->end()
            ->end()
            ->arrayNode('breadcrumbs')
                ->info('Breadcrumb definitions.')
                ->useAttributeAsKey('route')
                ->arrayPrototype()
                    ->children()
                        ->scalarNode('label')
                            ->info('Breadcrumb label.')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->arrayNode('parent')
                            ->children()
                                ->scalarNode('route')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                                ->arrayNode('parameters')
                                    ->info('Breadcrumb route parameters.')
                                    ->defaultValue([])
                                    ->variablePrototype()->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('parameters')
                            ->info('Breadcrumb route parameters.')
                            ->defaultValue([])
                            ->variablePrototype()->end()
                        ->end()
                    ->end()
                ->end()
                ->defaultValue([])
            ->end()
            ->scalarNode('template_path')
                ->info('Breadcrumbs template path.')
                ->cannotBeEmpty()
                ->defaultValue('@JmfBreadcrumbs/bootstrap/breadcrumbs.html.twig')
            ->end()
            ->scalarNode('twig_functions_prefix')
                ->info('Twig functions prefix.')
                ->defaultValue(BreadcrumbsExtension::PREFIX_DEFAULT)
            ->end()
            ->enumNode('current_breadcrumb_not_found_strategy')
                ->info("Behavior when the current route's breadcrumb is not found ('fail' or 'hide').")
                ->values(array_map(
                    static fn (CurrentBreadcrumbNotFoundBehavior $strategy): string => $strategy->value,
                             CurrentBreadcrumbNotFoundBehavior::cases(),
                ))
                ->defaultValue(CurrentBreadcrumbNotFoundBehavior::HIDE->value)
            ->end()
        ->end()
    ;
};
