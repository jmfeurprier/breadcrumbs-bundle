<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Tests\Model;

use Jmf\Breadcrumbs\Model\Breadcrumb;
use Jmf\Breadcrumbs\Model\CurrentBreadcrumbs;
use PHPUnit\Framework\TestCase;

final class CurrentBreadcrumbsTest extends TestCase
{
    /**
     * @var Breadcrumb[]
     */
    private array $breadcrumbs = [];

    public function testGetBreadcrumbsWithEmptyList(): void
    {
        $currentBreadcrumbs = $this->getInstance();

        self::assertEmpty(iterator_to_array($currentBreadcrumbs->getBreadcrumbs()));
    }

    public function testGetBreadcrumbsWithOneItem(): void
    {
        $breadcrumb = $this->givenBreadcrumb('label', 'route_name');

        $currentBreadcrumbs = $this->getInstance();

        self::assertCount(1, iterator_to_array($currentBreadcrumbs->getBreadcrumbs()));
        self::assertContains($breadcrumb, iterator_to_array($currentBreadcrumbs->getBreadcrumbs()));
    }

    public function testGetBreadcrumbsWithManyItems(): void
    {
        $breadcrumbPrimary   = $this->givenBreadcrumb('label_1', 'route_name_1');
        $breadcrumbSecondary = $this->givenBreadcrumb('label_2', 'route_name_2');

        $currentBreadcrumbs = $this->getInstance();

        self::assertCount(2, iterator_to_array($currentBreadcrumbs->getBreadcrumbs()));
        self::assertContains($breadcrumbPrimary, iterator_to_array($currentBreadcrumbs->getBreadcrumbs()));
        self::assertContains($breadcrumbSecondary, iterator_to_array($currentBreadcrumbs->getBreadcrumbs()));
    }

    public function testGetCurrentBreadcrumbWithEmptyList(): void
    {
        $currentBreadcrumbs = $this->getInstance();

        self::assertNull($currentBreadcrumbs->tryGetCurrentBreadcrumb());
    }

    public function testGetCurrentBreadcrumbWithOneItem(): void
    {
        $breadcrumb = $this->givenBreadcrumb('label', 'route_name');

        $currentBreadcrumbs = $this->getInstance();

        self::assertSame($breadcrumb, $currentBreadcrumbs->tryGetCurrentBreadcrumb());
    }

    public function testGetCurrentBreadcrumbWithManyItems(): void
    {
        $this->givenBreadcrumb('label_1', 'route_name_1');
        $breadcrumbSecondary = $this->givenBreadcrumb('label_2', 'route_name_2');

        $currentBreadcrumbs = $this->getInstance();

        self::assertSame($breadcrumbSecondary, $currentBreadcrumbs->tryGetCurrentBreadcrumb());
    }

    public function testGetPreviousBreadcrumbWithEmptyList(): void
    {
        $currentBreadcrumbs = $this->getInstance();

        self::assertNull($currentBreadcrumbs->tryGetPreviousBreadcrumb());
    }

    public function testGetPreviousBreadcrumbWithOneItem(): void
    {
        $this->givenBreadcrumb('label', 'route_name');

        $currentBreadcrumbs = $this->getInstance();

        self::assertNull($currentBreadcrumbs->tryGetPreviousBreadcrumb());
    }

    public function testGetPreviousBreadcrumbWithManyItems(): void
    {
        $breadcrumbPrimary = $this->givenBreadcrumb('label_1', 'route_name_1');
        $this->givenBreadcrumb('label_2', 'route_name_2');

        $currentBreadcrumbs = $this->getInstance();

        self::assertSame($breadcrumbPrimary, $currentBreadcrumbs->tryGetPreviousBreadcrumb());
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

    private function getInstance(): CurrentBreadcrumbs
    {
        return new CurrentBreadcrumbs(
            $this->breadcrumbs,
        );
    }
}
