<?php

namespace Jmf\Breadcrumbs\Configuration;

use Override;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

readonly class CacheableBreadcrumbConfigurationRepositoryFactory implements
    BreadcrumbConfigurationRepositoryFactoryInterface
{
    public function __construct(
        private CacheInterface $cache,
        private BreadcrumbConfigurationRepositoryFactoryInterface $breadcrumbConfigurationRepositoryFactory,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Override]
    public function make(): BreadcrumbConfigurationRepositoryInterface
    {
        return $this->cache->get(
            $this->getCacheKey(),
            $this->doMake(...),
        );
    }

    private function getCacheKey(): string
    {
        return md5(
            serialize(
                [
                    self::class,
                ],
            ),
        );
    }

    private function doMake(ItemInterface $item): BreadcrumbConfigurationRepositoryInterface
    {
        return $this->breadcrumbConfigurationRepositoryFactory->make();
    }
}
