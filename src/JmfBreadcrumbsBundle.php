<?php

namespace Jmf\Breadcrumbs;

use Jmf\Breadcrumbs\Configuration\BreadcrumbConfigurationRepositoryFactory;
use Jmf\Breadcrumbs\Configuration\BreadcrumbConfigurationRepositoryFactoryInterface;
use Jmf\Breadcrumbs\Configuration\BreadcrumbConfigurationRepositoryInterface;
use Jmf\Breadcrumbs\Configuration\CacheableBreadcrumbConfigurationRepositoryFactory;
use Jmf\Breadcrumbs\Twig\BreadcrumbsExtension;
use Override;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Contracts\Cache\CacheInterface;

class JmfBreadcrumbsBundle extends AbstractBundle
{
    protected string $extensionAlias = 'jmf_breadcrumbs';

    #[Override]
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->import('../config/definition.php');
    }

    #[Override]
    public function loadExtension(
        array $config,
        ContainerConfigurator $container,
        ContainerBuilder $builder,
    ): void {
        $container->import('../config/services.yaml');

        $container->services()
            ->set(BreadcrumbConfigurationRepositoryInterface::class)
            ->autowire()
            ->factory(
                [
                    new Reference(BreadcrumbConfigurationRepositoryFactoryInterface::class),
                    'make',
                ],
            )
        ;

        if (interface_exists(CacheInterface::class)) {
            $container->services()
                ->set(BreadcrumbConfigurationRepositoryFactory::class)
                ->autowire()
                ->arg('$config', $config['breadcrumbs'])
            ;

            $container->services()
                ->set(BreadcrumbConfigurationRepositoryFactoryInterface::class)
                ->autowire()
                ->class(CacheableBreadcrumbConfigurationRepositoryFactory::class)
                ->arg(
                    '$breadcrumbConfigurationRepositoryFactory',
                    new Reference(BreadcrumbConfigurationRepositoryFactory::class),
                )
            ;
        } else {
            $container->services()
                ->set(BreadcrumbConfigurationRepositoryFactoryInterface::class)
                ->autowire()
                ->class(BreadcrumbConfigurationRepositoryFactory::class)
                ->arg('$config', $config['breadcrumbs'])
            ;
        }

        $container->services()
            ->set(BreadcrumbsExtension::class)
            ->autowire()
            ->arg('$templatePath', $config['template_path'])
            ->arg('$prefix', $config['twig_functions_prefix'])
            ->tag('twig.extension')
        ;
    }
}
