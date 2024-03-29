<?php

namespace Jmf\Breadcrumbs\DependencyInjection;

use Exception;
use Jmf\Breadcrumbs\Configuration\BreadcrumbConfigurationRepositoryFactory;
use Jmf\Breadcrumbs\Configuration\BreadcrumbConfigurationRepositoryFactoryInterface;
use Jmf\Breadcrumbs\Configuration\BreadcrumbConfigurationRepositoryInterface;
use Jmf\Breadcrumbs\Configuration\CacheableBreadcrumbConfigurationRepositoryFactory;
use Jmf\Breadcrumbs\Twig\BreadcrumbsExtension;
use Override;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Contracts\Cache\CacheInterface;

class JmfBreadcrumbsExtension extends Extension
{
    /**
     * @throws Exception
     */
    #[Override]
    public function load(
        array $configs,
        ContainerBuilder $container,
    ): void {
        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yaml');

        $container->autowire(BreadcrumbConfigurationRepositoryInterface::class)
            ->setFactory(
                [
                    new Reference(BreadcrumbConfigurationRepositoryFactoryInterface::class),
                    'make',
                ]
            )
        ;

        if (interface_exists(CacheInterface::class)) {
            $container->autowire(BreadcrumbConfigurationRepositoryFactory::class)
                ->setArgument('$config', $config['breadcrumbs'])
            ;

            $container->autowire(BreadcrumbConfigurationRepositoryFactoryInterface::class)
                ->setClass(CacheableBreadcrumbConfigurationRepositoryFactory::class)
                ->setArgument(
                    '$breadcrumbConfigurationRepositoryFactory',
                    new Reference(BreadcrumbConfigurationRepositoryFactory::class),
                )
            ;
        } else {
            $container->autowire(BreadcrumbConfigurationRepositoryFactoryInterface::class)
                ->setClass(BreadcrumbConfigurationRepositoryFactory::class)
                ->setArgument('$config', $config['breadcrumbs'])
            ;
        }

        $container->autowire(BreadcrumbsExtension::class)
            ->setArgument('$templatePath', $config['template_path'])
            ->setArgument('$prefix', $config['twig_functions_prefix'])
            ->addTag('twig.extension')
        ;
    }

    #[Override]
    public function getNamespace(): string
    {
        return '';
    }

    #[Override]
    public function getXsdValidationBasePath(): bool
    {
        return false;
    }

    #[Override]
    public function getAlias(): string
    {
        return 'jmf_breadcrumbs';
    }
}
