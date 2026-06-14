<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs;

use Jmf\Breadcrumbs\Configuration\BreadcrumbsConfigurationLoader;
use Jmf\Breadcrumbs\Exception\BreadcrumbConfigurationException;
use Jmf\Breadcrumbs\Repository\BreadcrumbConfigurationRepositoryFactory;
use Jmf\Breadcrumbs\Repository\BreadcrumbConfigurationRepositoryFactoryInterface;
use Jmf\Breadcrumbs\Repository\BreadcrumbConfigurationRepositoryInterface;
use Jmf\Breadcrumbs\Twig\BreadcrumbsExtension;
use Override;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class JmfBreadcrumbsBundle extends AbstractBundle
{
    protected string $extensionAlias = 'jmf_breadcrumbs';

    public function __construct(
        private readonly BreadcrumbsConfigurationLoader $breadcrumbsConfigurationLoader = new BreadcrumbsConfigurationLoader(),
    ) {
    }

    #[Override]
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->import('../config/definition.php');
    }

    /**
     * @param array<string, mixed> $config
     *
     * @throws BreadcrumbConfigurationException
     */
    #[Override]
    public function loadExtension(
        array $config,
        ContainerConfigurator $container,
        ContainerBuilder $builder,
    ): void {
        $container->import('../config/services.yaml');

        $config['breadcrumbs'] = $this->breadcrumbsConfigurationLoader->load($config, $builder, $this->extensionAlias);

        $container->services()
            ->set(BreadcrumbConfigurationRepositoryInterface::class)
            ->autowire()
            ->factory(
                [
                    new Reference(BreadcrumbConfigurationRepositoryFactoryInterface::class),
                    'create',
                ],
            )
        ;

        $container->services()
            ->set(BreadcrumbConfigurationRepositoryFactoryInterface::class)
            ->autowire()
            ->class(BreadcrumbConfigurationRepositoryFactory::class)
            ->arg('$config', $config['breadcrumbs'])
        ;

        $container->services()
            ->set(BreadcrumbsExtension::class)
            ->autowire()
            ->arg('$templatePath', $config['template_path'])
            ->arg('$prefix', $config['twig_functions_prefix'])
            ->tag('twig.extension')
        ;
    }
}
