<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Tests\Model;

use Jmf\Breadcrumbs\Model\Breadcrumb;
use Jmf\Breadcrumbs\Model\BreadcrumbCollection;
use PHPUnit\Framework\TestCase;

final class BreadcrumbCollectionTest extends TestCase
{
    /**
     * @var Breadcrumb[]
     */
    private array $breadcrumbs = [];

    public function testGetBreadcrumbsWithEmptyList(): void
    {
        $breadcrumbCollection = $this->getInstance();

        self::assertEmpty($breadcrumbCollection->all());
    }

    public function testGetBreadcrumbsWithOneItem(): void
    {
        $breadcrumb = $this->givenBreadcrumb('label', 'route_name');

        $breadcrumbCollection = $this->getInstance();

        self::assertCount(1, $breadcrumbCollection->all());
        self::assertContains($breadcrumb, $breadcrumbCollection->all());
    }

    public function testGetBreadcrumbsWithManyItems(): void
    {
        $breadcrumbPrimary   = $this->givenBreadcrumb('label_1', 'route_name_1');
        $breadcrumbSecondary = $this->givenBreadcrumb('label_2', 'route_name_2');

        $breadcrumbCollection = $this->getInstance();

        self::assertCount(2, $breadcrumbCollection->all());
        self::assertContains($breadcrumbPrimary, $breadcrumbCollection->all());
        self::assertContains($breadcrumbSecondary, $breadcrumbCollection->all());
    }

    public function testGetCurrentBreadcrumbWithEmptyList(): void
    {
        $breadcrumbCollection = $this->getInstance();

        self::assertNull($breadcrumbCollection->tryGetCurrentBreadcrumb());
    }

    public function testGetCurrentBreadcrumbWithOneItem(): void
    {
        $breadcrumb = $this->givenBreadcrumb('label', 'route_name');

        $breadcrumbCollection = $this->getInstance();

        self::assertSame($breadcrumb, $breadcrumbCollection->tryGetCurrentBreadcrumb());
    }

    public function testGetCurrentBreadcrumbWithManyItems(): void
    {
        $this->givenBreadcrumb('label_1', 'route_name_1');
        $breadcrumbSecondary = $this->givenBreadcrumb('label_2', 'route_name_2');

        $breadcrumbCollection = $this->getInstance();

        self::assertSame($breadcrumbSecondary, $breadcrumbCollection->tryGetCurrentBreadcrumb());
    }

    public function testGetPreviousBreadcrumbWithEmptyList(): void
    {
        $breadcrumbCollection = $this->getInstance();

        self::assertNull($breadcrumbCollection->tryGetPreviousBreadcrumb());
    }

    public function testGetPreviousBreadcrumbWithOneItem(): void
    {
        $this->givenBreadcrumb('label', 'route_name');

        $breadcrumbCollection = $this->getInstance();

        self::assertNull($breadcrumbCollection->tryGetPreviousBreadcrumb());
    }

    public function testGetPreviousBreadcrumbWithManyItems(): void
    {
        $breadcrumbPrimary = $this->givenBreadcrumb('label_1', 'route_name_1');
        $this->givenBreadcrumb('label_2', 'route_name_2');

        $breadcrumbCollection = $this->getInstance();

        self::assertSame($breadcrumbPrimary, $breadcrumbCollection->tryGetPreviousBreadcrumb());
    }

    /**
     * @param array<string, mixed> $routeParameters
     */
    private function givenBreadcrumb(
        string $label,
        string $routeName,
        array $routeParameters = [],
    ): Breadcrumb {
        $breadcrumb = new Breadcrumb(
            $label,
            $routeName,
            $routeParameters,
        );

        $this->breadcrumbs[] = $breadcrumb;

        return $breadcrumb;
    }

    private function getInstance(): BreadcrumbCollection
    {
        return new BreadcrumbCollection(
            $this->breadcrumbs,
        );
    }
}
