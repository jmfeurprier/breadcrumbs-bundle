<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Tests\Registry;

use Jmf\Breadcrumbs\Compilation\BreadcrumbDefinitionCollectionCompiler;
use Jmf\Breadcrumbs\Compilation\BreadcrumbDefinitionCompiler;
use Jmf\Breadcrumbs\Compilation\ParentBreadcrumbDefinitionCompiler;
use Jmf\Breadcrumbs\Registry\BreadcrumbDefinitionRegistryFactory;
use Jmf\Breadcrumbs\Registry\BreadcrumbDefinitionRegistryInterface;
use PHPUnit\Framework\TestCase;

final class BreadcrumbDefinitionRegistryFactoryTest extends TestCase
{
    private BreadcrumbDefinitionRegistryFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new BreadcrumbDefinitionRegistryFactory(
            breadcrumbDefinitionCollectionCompiler: new BreadcrumbDefinitionCollectionCompiler(
                new BreadcrumbDefinitionCompiler(
                    new ParentBreadcrumbDefinitionCompiler(),
                ),
            ),
            config: [],
        );
    }

    public function testCreateReturnsRegistry(): void
    {
        $breadcrumbDefinitionRegistry = $this->factory->create();

        self::assertInstanceOf(BreadcrumbDefinitionRegistryInterface::class, $breadcrumbDefinitionRegistry);
    }

    public function testCreateReturnsRegistryWithDefinitions(): void
    {
        $breadcrumbDefinitionRegistryFactory = new BreadcrumbDefinitionRegistryFactory(
            breadcrumbDefinitionCollectionCompiler: new BreadcrumbDefinitionCollectionCompiler(
                new BreadcrumbDefinitionCompiler(
                    new ParentBreadcrumbDefinitionCompiler(),
                ),
            ),
            config: [
                'app_home' => ['label' => 'Home'],
            ],
        );

        $breadcrumbDefinitionRegistry = $breadcrumbDefinitionRegistryFactory->create();

        self::assertNotNull($breadcrumbDefinitionRegistry->tryGet('app_home'));
        self::assertNull($breadcrumbDefinitionRegistry->tryGet('app_unknown'));
    }
}
