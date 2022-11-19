<?php

namespace tests;

use Exception;
use PreventRaceCondition\SimpleCacheFromCacheItemPool;
use PHPUnit\Framework\TestCase;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class SimpleCacheFromCacheItemPoolTest extends TestCase
{

    private function getCache(): SimpleCacheFromCacheItemPool
    {
        $psr6Cache = new FilesystemAdapter();
        return new SimpleCacheFromCacheItemPool($psr6Cache);
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function testShouldSetAndExpire(): void
    {
        $cache = $this->getCache();

        $someValue = 'value' . random_bytes(100);
        $key = 'some_key';

        $cache->set($key, $someValue, 1);

        $this->assertTrue($cache->has($key));
        $this->assertEquals($someValue, $cache->get($key));

        sleep(1); // wait ttl

        $this->assertFalse($cache->has($key));
        $this->assertNull($cache->get($key));
        $this->assertEquals('default', $cache->get($key, 'default'));
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function testShouldSetAndGetMultiple(): void
    {
        $cache = $this->getCache();

        $cacheItems = [];
        $amount = random_int(5, 100);
        for ($i = 0; $i <= $amount; $i++) {
            $ransomKey = 'key_' . $i . '_' . random_int(10, 1000);
            $ransomValue = random_bytes(10);
            $cacheItems[$ransomKey] = $ransomValue;
        }
        $cache->setMultiple($cacheItems, 1);

        $cacheItemsFromCache = $cache->getMultiple(array_keys($cacheItems));
        $count = 0;
        foreach ($cacheItemsFromCache as $key => $value) {
            $this->assertEquals($cacheItems[$key], $value);
            $count++;
        }
        $this->assertEquals(count($cacheItems), $count);

        sleep(1); // wait ttl

        $cacheItemsFromCache = $cache->getMultiple(array_keys($cacheItems));
        $count = 0;
        foreach ($cacheItemsFromCache as $key => $value) {
            $this->assertEquals(null, $value);
            $count++;
        }
        $this->assertEquals(count($cacheItems), $count);

        $cacheItemsFromCache = $cache->getMultiple(array_keys($cacheItems), 'default');
        $count = 0;
        foreach ($cacheItemsFromCache as $key => $value) {
            $this->assertEquals('default', $value);
            $count++;
        }
        $this->assertEquals(count($cacheItems), $count);
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function testDeleteMultiple(): void
    {
        $cache = $this->getCache();

        $cacheItems = [];
        $amount = random_int(5, 100);
        for ($i = 0; $i <= $amount; $i++) {
            $ransomKey = 'key_' . $i . '_' . random_int(10, 1000);
            $ransomValue = random_bytes(10);
            $cacheItems[$ransomKey] = $ransomValue;
        }
        $cache->setMultiple($cacheItems, 1);

        foreach ($cacheItems as $key => $value) {
            $this->assertEquals($value, $cache->get($key));
        }

        $cache->deleteMultiple(array_keys($cacheItems));

        foreach ($cacheItems as $key => $value) {
            $this->assertEquals(null, $cache->get($key));
        }
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function testDelete(): void
    {
        $cache = $this->getCache();

        $someValue = 'value' . random_bytes(100);
        $key = 'some_key';

        $cache->set($key, $someValue, 1);

        $this->assertTrue($cache->has($key));
        $this->assertEquals($someValue, $cache->get($key));

        $cache->delete($key);

        $this->assertFalse($cache->has($key));
        $this->assertNull($cache->get($key));
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function testClear(): void
    {
        $cache = $this->getCache();

        $someValue = 'value' . random_bytes(100);
        $key = 'some_key';

        $cache->set($key, $someValue, 1);

        $this->assertTrue($cache->has($key));
        $this->assertEquals($someValue, $cache->get($key));

        $cache->clear();

        $this->assertFalse($cache->has($key));
        $this->assertNull($cache->get($key));
    }

}
