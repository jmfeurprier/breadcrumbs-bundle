<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Tests\Resolution;

use Exception;
use Jmf\Breadcrumbs\Exception\PreviousBreadcrumbNotFoundException;
use Jmf\Breadcrumbs\Exception\PreviousBreadcrumbResolutionException;
use Jmf\Breadcrumbs\Exception\PreviousBreadcrumbResolutionFailedException;
use Jmf\Breadcrumbs\Model\Breadcrumb;
use Jmf\Breadcrumbs\Model\CurrentBreadcrumbs;
use Jmf\Breadcrumbs\Resolution\CurrentBreadcrumbsResolverInterface;
use Jmf\Breadcrumbs\Resolution\PreviousBreadcrumbResolver;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Throwable;

final class PreviousBreadcrumbResolverTest extends TestCase
{
    /**
     * @var array<string, mixed>
     */
    private array $context = [];

    /**
     * @var Breadcrumb[]
     */
    private array $currentBreadcrumbs = [];

    private PreviousBreadcrumbResolver $previousBreadcrumbResolver;

    private CurrentBreadcrumbsResolverInterface & MockObject $currentBreadcrumbsResolver;

    private ?Throwable $currentBreadcrumbsResolverException = null;

    private Breadcrumb $result;

    protected function setUp(): void
    {
        $this->currentBreadcrumbsResolver = $this->createMock(CurrentBreadcrumbsResolverInterface::class);

        $this->previousBreadcrumbResolver = new PreviousBreadcrumbResolver(
            $this->currentBreadcrumbsResolver,
        );
    }

    public function testResolveReturnsPreviousBreadcrumbWhenAvailable(): void
    {
        $previousBreadcrumb = $this->createBreadcrumb('Previous', 'route_previous');

        $this->givenContext(['key' => 'value']);
        $this->givenBreadcrumbs(
            [
                $previousBreadcrumb,
                $this->createBreadcrumb('Current', 'route_current'),
            ],
        );

        $this->whenResolve();

        $this->thenResult($previousBreadcrumb);
    }

    public function testResolveThrowsExceptionWhenNoPreviousBreadcrumbExists(): void
    {
        $this->givenContext(['key' => 'value']);
        $this->givenBreadcrumbs(
            [
                $this->createBreadcrumb('Current', 'route_current'),
            ],
        );

        $this->expectException(PreviousBreadcrumbNotFoundException::class);

        try {
            $this->whenResolve();
        } catch (PreviousBreadcrumbNotFoundException $e) {
            self::assertSame($this->context, $e->getContext());

            throw $e;
        }
    }

    public function testResolveThrowsExceptionWhenBreadcrumbsListIsEmpty(): void
    {
        $this->givenContext(['key' => 'value']);
        $this->givenBreadcrumbs([]);

        $this->expectException(PreviousBreadcrumbNotFoundException::class);

        try {
            $this->whenResolve();
        } catch (PreviousBreadcrumbNotFoundException $e) {
            self::assertSame($this->context, $e->getContext());

            throw $e;
        }
    }

    public function testResolveThrowsExceptionWhenResolverFails(): void
    {
        $resolverException = new Exception('Resolver error');

        $this->givenContext(['key' => 'value']);
        $this->givenCurrentBreadcrumbCollectionResolverException($resolverException);

        $this->expectException(PreviousBreadcrumbResolutionException::class);

        try {
            $this->whenResolve();
        } catch (PreviousBreadcrumbResolutionFailedException $e) {
            self::assertSame($resolverException, $e->getPrevious());
            self::assertSame($this->context, $e->getContext());

            throw $e;
        }
    }

    /**
     * @param array<string, mixed> $context
     */
    private function givenContext(array $context): void
    {
        $this->context = $context;
    }

    private function givenCurrentBreadcrumbCollectionResolverException(Throwable $exception): void
    {
        $this->currentBreadcrumbsResolverException = $exception;
    }

    private function createBreadcrumb(
        string $label,
        string $routeName,
    ): Breadcrumb {
        return new Breadcrumb(
            label:           $label,
            routeName:       $routeName,
            routeParameters: [],
        );
    }

    /**
     * @param Breadcrumb[] $breadcrumbs
     */
    private function givenBreadcrumbs(array $breadcrumbs): void
    {
        $this->currentBreadcrumbs = $breadcrumbs;
    }

    /**
     * @throws PreviousBreadcrumbResolutionException
     */
    private function whenResolve(): void
    {
        if ($this->currentBreadcrumbsResolverException instanceof Throwable) {
            $this->currentBreadcrumbsResolver->expects(self::once())
                ->method('resolve')
                ->with($this->context)
                ->willThrowException($this->currentBreadcrumbsResolverException)
            ;
        } else {
            $this->currentBreadcrumbsResolver->expects(self::once())
                ->method('resolve')
                ->with($this->context)
                ->willReturn(
                    new CurrentBreadcrumbs(
                        $this->currentBreadcrumbs,
                    ),
                )
            ;
        }

        $this->result = $this->previousBreadcrumbResolver->resolve($this->context);
    }

    private function thenResult(Breadcrumb $breadcrumb): void
    {
        self::assertSame($breadcrumb, $this->result);
    }
}
