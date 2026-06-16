<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Tests\Resolution;

use Jmf\Breadcrumbs\Exception\BreadcrumbContextResolutionException;
use Jmf\Breadcrumbs\Resolution\ContextResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyAccess\PropertyAccessor;

final class ContextResolverTest extends TestCase
{
    private ContextResolver $contextResolver;

    /**
     * @var array<string, mixed>
     */
    private array $current = [];

    /**
     * @var array<string, string>
     */
    private array $input = [];

    /**
     * @var array<string, mixed>
     */
    private array $result;

    protected function setUp(): void
    {
        $this->contextResolver = new ContextResolver(new PropertyAccessor());
    }

    public function testResolveWithEmptyInputReturnsCurrent(): void
    {
        $this->givenCurrent(['key' => 'value']);
        $this->givenInput([]);

        $this->whenResolve();

        $this->thenResult(['key' => 'value']);
    }

    public function testResolveMapsDirectProperty(): void
    {
        $this->givenCurrent(['foo' => 'bar']);
        $this->givenInput(['baz' => 'foo']);

        $this->whenResolve();

        $this->thenResult(['foo' => 'bar', 'baz' => 'bar']);
    }

    public function testResolveMapsNestedPropertyViaGetter(): void
    {
        $entity = new class {
            public function getName(): string
            {
                return 'Alice';
            }
        };

        $this->givenCurrent(['entity' => $entity]);
        $this->givenInput(['entityName' => 'entity.name']);

        $this->whenResolve();

        $this->thenResultContains('entityName', 'Alice');
    }

    public function testResolvePreservesExistingContextKeys(): void
    {
        $entity = new class {
            public function getId(): string
            {
                return '42';
            }
        };

        $this->givenCurrent(['entity' => $entity]);
        $this->givenInput(['entityId' => 'entity.id']);

        $this->whenResolve();

        $this->thenResultContains('entity', $entity);
        $this->thenResultContains('entityId', '42');
    }

    public function testResolveThrowsOnFailedPropertyAccess(): void
    {
        $entity = new class {
            public function getName(): string
            {
                return 'Alice';
            }
        };

        $this->givenCurrent(['entity' => $entity]);
        $this->givenInput(['missing' => 'entity.nonExistentProperty']);

        $this->expectException(BreadcrumbContextResolutionException::class);

        try {
            $this->whenResolve();
        } catch (BreadcrumbContextResolutionException $e) {
            self::assertSame('missing', $e->getKey());
            self::assertSame('entity.nonExistentProperty', $e->getValue());

            throw $e;
        }
    }

    /**
     * @param array<string, mixed> $current
     */
    private function givenCurrent(array $current): void
    {
        $this->current = $current;
    }

    /**
     * @param array<string, string> $input
     */
    private function givenInput(array $input): void
    {
        $this->input = $input;
    }

    private function whenResolve(): void
    {
        $this->result = $this->contextResolver->resolve($this->current, $this->input);
    }

    /**
     * @param array<string, mixed> $expected
     */
    private function thenResult(array $expected): void
    {
        self::assertSame($expected, $this->result);
    }

    private function thenResultContains(string $key, mixed $value): void
    {
        self::assertArrayHasKey($key, $this->result);
        self::assertSame($value, $this->result[$key]);
    }
}
