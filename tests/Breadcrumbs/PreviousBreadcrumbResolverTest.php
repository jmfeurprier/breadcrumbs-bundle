<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Tests\Breadcrumbs;

use Exception;
use Jmf\Breadcrumbs\Breadcrumbs\Breadcrumb;
use Jmf\Breadcrumbs\Breadcrumbs\CurrentBreadcrumbs;
use Jmf\Breadcrumbs\Breadcrumbs\CurrentBreadcrumbsFetcherInterface;
use Jmf\Breadcrumbs\Breadcrumbs\PreviousBreadcrumbResolver;
use Jmf\Breadcrumbs\Exception\PreviousBreadcrumbResolutionException;
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
    private iterable $currentBreadcrumbs = [];

    private PreviousBreadcrumbResolver $previousBreadcrumbResolver;

    private CurrentBreadcrumbsFetcherInterface & MockObject $currentBreadcrumbsFetcher;

    private ?Throwable $currentBreadcrumbsFetcherException = null;

    private Breadcrumb $result;

    protected function setUp(): void
    {
        $this->currentBreadcrumbsFetcher = $this->createMock(CurrentBreadcrumbsFetcherInterface::class);

        $this->previousBreadcrumbResolver = new PreviousBreadcrumbResolver(
            $this->currentBreadcrumbsFetcher,
        );
    }

    public function testResolveReturnsPreviousBreadcrumbWhenAvailable(): void
    {
        $previousBreadcrumb = $this->createBreadcrumb('Previous', '/previous', 'route_previous');

        $this->givenContext(['key' => 'value']);
        $this->givenBreadcrumbs(
            [
                $previousBreadcrumb,
                $this->createBreadcrumb('Current', '/current', 'route_current'),
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
                $this->createBreadcrumb('Current', '/current', 'route_current'),
            ],
        );

        $this->expectException(PreviousBreadcrumbResolutionException::class);
        $this->expectExceptionMessage('Failed resolving previous Breadcrumb.');

        $this->whenResolve();
    }

    public function testResolveThrowsExceptionWhenBreadcrumbsListIsEmpty(): void
    {
        $this->givenContext(['key' => 'value']);
        $this->givenBreadcrumbs([]);

        $this->expectException(PreviousBreadcrumbResolutionException::class);
        $this->expectExceptionMessage('Failed resolving previous Breadcrumb.');

        $this->whenResolve();
    }

    public function testResolveThrowsExceptionWhenFetcherFails(): void
    {
        $fetcherException = new Exception('Fetcher error');

        $this->givenContext(['key' => 'value']);
        $this->givenCurrentBreadcrumbsFetcherException($fetcherException);

        $this->expectException(PreviousBreadcrumbResolutionException::class);
        $this->expectExceptionMessage('Failed resolving back URL: failed fetching current Breadcrumbs.');

        try {
            $this->whenResolve();
        } catch (PreviousBreadcrumbResolutionException $e) {
            self::assertSame($fetcherException, $e->getPrevious());

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

    private function givenCurrentBreadcrumbsFetcherException(Throwable $exception): void
    {
        $this->currentBreadcrumbsFetcherException = $exception;
    }

    private function createBreadcrumb(
        string $label,
        string $path,
        string $routeName,
    ): Breadcrumb {
        return new Breadcrumb($label, $path, $routeName);
    }

    /**
     * @param Breadcrumb[] $breadcrumbs
     */
    private function givenBreadcrumbs(iterable $breadcrumbs): void
    {
        $this->currentBreadcrumbs = $breadcrumbs;
    }

    /**
     * @throws PreviousBreadcrumbResolutionException
     */
    private function whenResolve(): void
    {
        if ($this->currentBreadcrumbsFetcherException instanceof Throwable) {
            $this->currentBreadcrumbsFetcher->expects(self::once())
                ->method('fetch')
                ->with($this->context)
                ->willThrowException($this->currentBreadcrumbsFetcherException)
            ;
        } else {
            $this->currentBreadcrumbsFetcher->expects(self::once())
                ->method('fetch')
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
