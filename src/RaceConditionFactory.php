<?php
declare(strict_types=1);

namespace PreventRaceCondition;

use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;

class RaceConditionFactory
{

    public static function fromSimpleCache(CacheInterface $cache): RaceCondition
    {
        return new RaceCondition($cache);
    }

    public static function fromCacheItemPool(CacheItemPoolInterface $cacheItemPool): RaceCondition
    {
        $simpleCache = new SimpleCacheFromCacheItemPool($cacheItemPool);
        return new RaceCondition($simpleCache);
    }


}