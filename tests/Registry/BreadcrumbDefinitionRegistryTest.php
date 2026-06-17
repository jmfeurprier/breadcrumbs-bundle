<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Tests\Registry;

use Jmf\Breadcrumbs\Definition\BreadcrumbDefinition;
use Jmf\Breadcrumbs\Definition\BreadcrumbDefinitionCollection;
use Jmf\Breadcrumbs\Definition\StringMap;
use Jmf\Breadcrumbs\Registry\BreadcrumbDefinitionRegistry;
use PHPUnit\Framework\TestCase;

final class BreadcrumbDefinitionRegistryTest extends TestCase
{
    private BreadcrumbDefinitionRegistry $registry;

    private BreadcrumbDefinition $homeDefinition;

    protected function setUp(): void
    {
        $this->homeDefinition = new BreadcrumbDefinition(
            routeName:                  'app_home',
            label:                      'Home',
            parameters:                 StringMap::createEmpty(),
            parentBreadcrumbDefinition: null,
        );

        $this->registry = new BreadcrumbDefinitionRegistry(
            new BreadcrumbDefinitionCollection([$this->homeDefinition]),
        );
    }

    public function testTryGetReturnsDefinitionWhenFound(): void
    {
        $result = $this->registry->tryGet('app_home');

        self::assertSame($this->homeDefinition, $result);
    }

    public function testTryGetReturnsNullWhenNotFound(): void
    {
        $result = $this->registry->tryGet('app_unknown');

        self::assertNull($result);
    }
}
