<?php

namespace Jmf\Breadcrumbs\Configuration;

use Jmf\Breadcrumbs\Exception\BreadcrumbConfigurationException;
use Override;
use Psr\Cache\CacheException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

readonly class CacheableBreadcrumbConfigurationRepositoryFactory implements
    BreadcrumbConfigurationRepositoryFactoryInterface
{
    private string $cacheKey;

    public function __construct(
        private CacheInterface $cache,
        private BreadcrumbConfigurationRepositoryFactoryInterface $breadcrumbConfigurationRepositoryFactory,
    ) {
        $this->cacheKey = $this->getCacheKey();
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

    /**
     * @throws CacheException
     */
    #[Override]
    public function make(): BreadcrumbConfigurationRepositoryInterface
    {
        return $this->cache->get(
            $this->cacheKey,
            $this->doMake(...),
        );
    }

    /**
     * @throws BreadcrumbConfigurationException
     */
    private function doMake(ItemInterface $item): BreadcrumbConfigurationRepositoryInterface
    {
        return $this->breadcrumbConfigurationRepositoryFactory->make();
    }
}
