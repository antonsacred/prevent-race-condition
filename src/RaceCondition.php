<?php
declare(strict_types=1);

namespace PreventRaceCondition;

use Psr\SimpleCache\CacheInterface;

class RaceCondition implements RaceConditionInterface
{

    private CacheInterface $cache;
    private string $prefix = 'prevent_race_cond_';
    private int $maxTime = 5 * 60;  // 5 minutes

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function isBusy(string $name): bool
    {
        return $this->cache->has($this->getCacheName($name));
    }

    public function lock(string $name): void
    {
        $this->cache->set($this->getCacheName($name), '1', $this->maxTime);
    }

    public function release(string $name): void
    {
        $this->cache->delete($this->getCacheName($name));
    }

    public function setPrefix(string $prefix): void
    {
        $this->prefix = $prefix;
    }

    public function setMaxTime(int $seconds): void
    {
        $this->maxTime = $seconds;
    }

    private function getCacheName(string $name): string
    {
        return $this->prefix . $name;
    }

}
