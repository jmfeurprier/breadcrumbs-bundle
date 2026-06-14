<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs;

use Jmf\Breadcrumbs\Configuration\BreadcrumbsConfigurationLoader;
use Jmf\Breadcrumbs\Exception\BreadcrumbConfigurationException;
use Override;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class JmfBreadcrumbsBundle extends AbstractBundle
{
    protected string $extensionAlias = 'jmf_breadcrumbs';

    private const array PARAMETERS_MAPPING = [
        'breadcrumbs'           => 'breadcrumbs_config',
        'template_path'         => 'template_path',
        'twig_functions_prefix' => 'twig_functions_prefix',
    ];

    private readonly BreadcrumbsConfigurationLoader $breadcrumbsConfigurationLoader;

    public function __construct(?BreadcrumbsConfigurationLoader $breadcrumbsConfigurationLoader = null)
    {
        $this->breadcrumbsConfigurationLoader = $breadcrumbsConfigurationLoader
            ?? new BreadcrumbsConfigurationLoader();
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

        $this->loadParameters($config, $container);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function loadParameters(array $config, ContainerConfigurator $container): void
    {
        foreach (self::PARAMETERS_MAPPING as $configKey => $parameterSuffix) {
            $container->parameters()->set("{$this->extensionAlias}.{$parameterSuffix}", $config[$configKey]);
        }
    }
}
