<?php

namespace Jmf\Breadcrumbs\Tests\Breadcrumbs;

use Jmf\Breadcrumbs\Breadcrumbs\Breadcrumb;
use Jmf\Breadcrumbs\Breadcrumbs\CurrentBreadcrumbs;
use PHPUnit\Framework\TestCase;

class CurrentBreadcrumbsTest extends TestCase
{
    /**
     * @var Breadcrumb[]
     */
    private array $breadcrumbs = [];

    public function testGetBreadcrumbsWithEmptyList(): void
    {
        $currentBreadcrumbs = $this->getInstance();

        $this->assertEmpty($currentBreadcrumbs->getBreadcrumbs());
    }

    public function testGetBreadcrumbsWithOneItem(): void
    {
        $breadcrumb = $this->givenBreadcrumb('label', 'path');

        $currentBreadcrumbs = $this->getInstance();

        $this->assertCount(1, $currentBreadcrumbs->getBreadcrumbs());
        $this->assertContains($breadcrumb, $currentBreadcrumbs->getBreadcrumbs());
    }

    public function testGetBreadcrumbsWithManyItems(): void
    {
        $breadcrumbPrimary   = $this->givenBreadcrumb('label_1', 'path_1');
        $breadcrumbSecondary = $this->givenBreadcrumb('label_2', 'path_2');

        $currentBreadcrumbs = $this->getInstance();

        $this->assertCount(2, $currentBreadcrumbs->getBreadcrumbs());
        $this->assertContains($breadcrumbPrimary, $currentBreadcrumbs->getBreadcrumbs());
        $this->assertContains($breadcrumbSecondary, $currentBreadcrumbs->getBreadcrumbs());
    }

    public function testGetCurrentBreadcrumbWithEmptyList(): void
    {
        $currentBreadcrumbs = $this->getInstance();

        $this->assertNull($currentBreadcrumbs->tryGetCurrentBreadcrumb());
    }

    public function testGetCurrentBreadcrumbWithOneItem(): void
    {
        $breadcrumb = $this->givenBreadcrumb('label', 'path');

        $currentBreadcrumbs = $this->getInstance();

        $this->assertSame($breadcrumb, $currentBreadcrumbs->tryGetCurrentBreadcrumb());
    }

    public function testGetCurrentBreadcrumbWithManyItems(): void
    {
        $this->givenBreadcrumb('label_1', 'path_1');
        $breadcrumbSecondary = $this->givenBreadcrumb('label_2', 'path_2');

        $currentBreadcrumbs = $this->getInstance();

        $this->assertSame($breadcrumbSecondary, $currentBreadcrumbs->tryGetCurrentBreadcrumb());
    }

    public function testGetPreviousBreadcrumbWithEmptyList(): void
    {
        $currentBreadcrumbs = $this->getInstance();

        $this->assertNull($currentBreadcrumbs->tryGetPreviousBreadcrumb());
    }

    public function testGetPreviousBreadcrumbWithOneItem(): void
    {
        $this->givenBreadcrumb('label', 'path');

        $currentBreadcrumbs = $this->getInstance();

        $this->assertNull($currentBreadcrumbs->tryGetPreviousBreadcrumb());
    }

    public function testGetPreviousBreadcrumbWithManyItems(): void
    {
        $breadcrumbPrimary = $this->givenBreadcrumb('label_1', 'path_1');
        $this->givenBreadcrumb('label_2', 'path_2');

        $currentBreadcrumbs = $this->getInstance();

        $this->assertSame($breadcrumbPrimary, $currentBreadcrumbs->tryGetPreviousBreadcrumb());
    }

    private function givenBreadcrumb(
        string $label,
        string $path,
    ): Breadcrumb {
        $breadcrumb = new Breadcrumb(
            $label,
            $path,
        );

        $this->breadcrumbs[] = $breadcrumb;

        return $breadcrumb;
    }

    private function getInstance(): CurrentBreadcrumbs
    {
        return new CurrentBreadcrumbs(
            $this->breadcrumbs
        );
    }
}
