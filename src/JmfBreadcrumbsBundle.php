<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs;

use Jmf\Breadcrumbs\Configuration\BreadcrumbsConfigurationLoader;
use Jmf\Breadcrumbs\Exception\BreadcrumbConfigurationException;
use Override;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

#[Exclude]
class JmfBreadcrumbsBundle extends AbstractBundle
{
    protected string $extensionAlias = 'jmf_breadcrumbs';

    private const array PARAMETERS_MAPPING = [
        'breadcrumbs'           => 'breadcrumbs_config',
        'template_path'         => 'template_path',
        'twig_functions_prefix' => 'twig_functions_prefix',
        'current_breadcrumb_not_found_strategy'
            => 'current_breadcrumb_not_found_strategy',
    ];

    public function __construct(
        private readonly BreadcrumbsConfigurationLoader $breadcrumbsConfigurationLoader = new BreadcrumbsConfigurationLoader(
        ),
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
        ContainerConfigurator $configurator,
        ContainerBuilder $container,
    ): void {
        $configurator->import('../config/services.yaml');

        $config['breadcrumbs'] = $this->breadcrumbsConfigurationLoader->load(
            $config,
            $container,
            $this->extensionAlias,
        );

        $this->loadParameters($config, $configurator);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function loadParameters(
        array $config,
        ContainerConfigurator $containerConfigurator,
    ): void {
        foreach (self::PARAMETERS_MAPPING as $configKey => $parameterSuffix) {
            $containerConfigurator->parameters()->set(
                sprintf(
                    "%s.%s",
                    $this->extensionAlias,
                    $parameterSuffix,
                ),
                $config[$configKey],
            );
        }
    }
}
