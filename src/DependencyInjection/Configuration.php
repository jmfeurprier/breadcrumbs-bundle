<?php

namespace Jmf\Breadcrumbs\DependencyInjection;

use Jmf\Breadcrumbs\Twig\BreadcrumbsExtension;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('jmf_breadcrumbs');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('breadcrumbs')
                    ->info('Breadcrumb definitions.')
                    ->useAttributeAsKey('route')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('label')
                                ->info('Breadcrumb label.')
                                ->isRequired()
                            ->end()
                            ->arrayNode('parent')
                                ->children()
                                    ->scalarNode('route')
                                        ->isRequired()
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
                ->scalarNode('twig_functions_prefix')
                    ->info('Twig functions prefix.')
                    ->defaultValue(BreadcrumbsExtension::PREFIX_DEFAULT)
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
