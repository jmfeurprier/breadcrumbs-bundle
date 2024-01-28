<?php

namespace Jmf\Breadcrumbs\Configuration;

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
    public function make(): BreadcrumbConfigurationRepositoryInterface
    {
        return $this->cache->get(
            $this->getCacheKey(),
            $this->getCallback(),
        );
    }

    private function getCacheKey(): string
    {
        return md5(
            serialize(
                [
                    self::class,
                ]
            )
        );
    }

    private function getCallback(): callable
    {
        return fn(
            ItemInterface $item,
        ) => $this->breadcrumbConfigurationRepositoryFactory->make();
    }
}
