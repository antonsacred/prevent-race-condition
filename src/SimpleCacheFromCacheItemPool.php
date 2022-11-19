<?php
declare(strict_types=1);

namespace PreventRaceCondition;

use Generator;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Psr\SimpleCache\CacheInterface;


class SimpleCacheFromCacheItemPool implements CacheInterface
{

    protected CacheItemPoolInterface $cacheItemPool;

    public function __construct(CacheItemPoolInterface $cacheItemPool)
    {
        $this->cacheItemPool = $cacheItemPool;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function get($key, $default = null): mixed
    {
        $item = $this->cacheItemPool->getItem($key);

        if (!$item->isHit()) {
            return $default;
        }

        return $item->get();
    }


    /**
     * @throws InvalidArgumentException
     */
    public function set($key, $value, $ttl = null): bool
    {
        $item = $this->cacheItemPool->getItem($key);
        $item->expiresAfter($ttl);

        $item->set($value);

        return $this->cacheItemPool->save($item);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function delete($key): bool
    {
        return $this->cacheItemPool->deleteItem($key);
    }

    public function clear(): bool
    {
        return $this->cacheItemPool->clear();
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $keysToGetItems = [];
        foreach ($keys as $key) {
            $keysToGetItems[] = $key;
        }
        $items = $this->cacheItemPool->getItems($keysToGetItems);

        return $this->generateValues($default, $items);
    }

    /**
     * @param CacheItemInterface[] $items
     */
    private function generateValues($default, iterable $items): Generator
    {
        foreach ($items as $key => $item) {
            if (!$item->isHit()) {
                yield $key => $default;
            } else {
                yield $key => $item->get();
            }
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setMultiple(iterable $values, $ttl = null): bool
    {
        $keys = [];
        $arrayValues = [];
        foreach ($values as $key => $value) {
            $keys[] = (string)$key;
            $arrayValues[$key] = $value;
        }

        /* @var $items CacheItemInterface[] */
        $items = $this->cacheItemPool->getItems($keys);

        $itemSuccess = true;

        foreach ($items as $key => $item) {
            $item->set($arrayValues[$key]);

            $item->expiresAfter($ttl);

            $itemSuccess = $itemSuccess && $this->cacheItemPool->saveDeferred($item);
        }

        return $itemSuccess && $this->cacheItemPool->commit();
    }

    /**
     * @throws InvalidArgumentException
     */
    public function deleteMultiple(iterable $keys): bool
    {
        $keysToDelete = [];
        foreach ($keys as $key) {
            $keysToDelete[] = $key;
        }
        return $this->cacheItemPool->deleteItems($keysToDelete);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function has($key): bool
    {
        return $this->cacheItemPool->hasItem($key);
    }

}
