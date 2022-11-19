<?php

namespace tests;

use PreventRaceCondition\RaceCondition;
use PreventRaceCondition\RaceConditionFactory;
use PHPUnit\Framework\TestCase;
use PreventRaceCondition\SimpleCacheFromCacheItemPool;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class RaceConditionFactoryTest extends TestCase
{
    private function getSimpleCache(): SimpleCacheFromCacheItemPool
    {
        $psr6Cache = new FilesystemAdapter();
        return new SimpleCacheFromCacheItemPool($psr6Cache);
    }

    private function getCacheItemPool(): CacheItemPoolInterface
    {
        return new FilesystemAdapter();
    }

    public function testShouldWork(): void
    {
        $raceCondition = RaceConditionFactory::fromCacheItemPool($this->getCacheItemPool());

        $this->assertInstanceOf(RaceCondition::class, $raceCondition);

        $raceCondition = RaceConditionFactory::fromSimpleCache($this->getSimpleCache());

        $this->assertInstanceOf(RaceCondition::class, $raceCondition);
    }
}
