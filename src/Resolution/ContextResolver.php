<?php

declare(strict_types=1);

namespace Jmf\Breadcrumbs\Resolution;

use Jmf\Breadcrumbs\Exception\BreadcrumbContextResolutionException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Throwable;
use Webmozart\Assert\Assert;

readonly class ContextResolver
{
    public function __construct(
        private PropertyAccessorInterface $propertyAccessor,
    ) {
    }

    /**
     * @param array<string, mixed>  $current
     * @param array<string, string> $input
     *
     * @return array<string, mixed>
     *
     * @throws BreadcrumbContextResolutionException
     */
    public function resolve(
        array $current,
        array $input,
    ): array {
        $output = $current;

        foreach ($input as $key => $value) {
            Assert::string($value);

            try {
                $output[$key] = $this->propertyAccessor->getValue((object) $output, $value);
            } catch (Throwable $e) {
                throw new BreadcrumbContextResolutionException(
                    key:      $key,
                    value:    $value,
                    context:  $output,
                    previous: $e,
                );
            }
        }

        return $output;
    }
}
