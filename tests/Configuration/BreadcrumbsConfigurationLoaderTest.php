<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Tests\Configuration;

use Jmf\Breadcrumbs\Configuration\BreadcrumbsConfigurationLoader;
use Jmf\Breadcrumbs\Exception\BreadcrumbRouteDefinitionConflictException;
use Jmf\Breadcrumbs\Exception\DuplicateBreadcrumbRouteDefinitionException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class BreadcrumbsConfigurationLoaderTest extends TestCase
{
    private BreadcrumbsConfigurationLoader $loader;

    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->loader    = new BreadcrumbsConfigurationLoader();
        $this->container = new ContainerBuilder();
    }

    public function testLoadWithNonExistentPathReturnsInlineOnly(): void
    {
        $config = [
            'paths'       => [__DIR__ . '/fixtures/nonexistent'],
            'breadcrumbs' => [
                'route_home' => ['label' => 'Home'],
            ],
        ];

        $result = $this->loader->load($config, $this->container, 'jmf_breadcrumbs');

        self::assertSame(['route_home' => ['label' => 'Home']], $result);
    }

    public function testLoadWithEmptyDirectoryReturnsInlineOnly(): void
    {
        $config = [
            'paths'       => [__DIR__ . '/fixtures/empty'],
            'breadcrumbs' => [
                'route_home' => ['label' => 'Home'],
            ],
        ];

        $result = $this->loader->load($config, $this->container, 'jmf_breadcrumbs');

        self::assertSame(['route_home' => ['label' => 'Home']], $result);
    }

    public function testLoadMergesYamlFilesWithInline(): void
    {
        $config = [
            'paths'       => [__DIR__ . '/fixtures/single'],
            'breadcrumbs' => [
                'route_about' => ['label' => 'About'],
            ],
        ];

        $result = $this->loader->load($config, $this->container, 'jmf_breadcrumbs');

        self::assertArrayHasKey('route_home', $result);
        self::assertArrayHasKey('route_about', $result);
        self::assertSame('Home', $result['route_home']['label']);
        self::assertSame('About', $result['route_about']['label']);
    }

    public function testLoadLoadsMultipleYamlFiles(): void
    {
        $config = [
            'paths'       => [__DIR__ . '/fixtures/multiple'],
            'breadcrumbs' => [],
        ];

        $result = $this->loader->load($config, $this->container, 'jmf_breadcrumbs');

        self::assertArrayHasKey('route_a', $result);
        self::assertArrayHasKey('route_b', $result);
    }

    public function testLoadThrowsOnDuplicateRouteAcrossPaths(): void
    {
        $config = [
            'paths'       => [
                __DIR__ . '/fixtures/duplicate/dir1',
                __DIR__ . '/fixtures/duplicate/dir2',
            ],
            'breadcrumbs' => [],
        ];

        $this->expectException(DuplicateBreadcrumbRouteDefinitionException::class);

        try {
            $this->loader->load($config, $this->container, 'jmf_breadcrumbs');
        } catch (DuplicateBreadcrumbRouteDefinitionException $e) {
            self::assertSame('route_home', $e->getRouteName());

            throw $e;
        }
    }

    public function testLoadThrowsOnConflictBetweenPathsAndInline(): void
    {
        $config = [
            'paths'       => [__DIR__ . '/fixtures/single'],
            'breadcrumbs' => [
                'route_home' => ['label' => 'Home inline'],
            ],
        ];

        $this->expectException(BreadcrumbRouteDefinitionConflictException::class);

        try {
            $this->loader->load($config, $this->container, 'jmf_breadcrumbs');
        } catch (BreadcrumbRouteDefinitionConflictException $e) {
            self::assertContains('route_home', $e->getRouteNames());

            throw $e;
        }
    }
}
