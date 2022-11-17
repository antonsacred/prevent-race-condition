<?php
declare(strict_types=1);

// todo: set cache interface

class RaceCondition
{

    private $cache;
    private $namespace = '_';
    private $maxTime = 5 * 60;

    public function __construct($cache)
    {
        $this->cache = $cache;
    }

    public function isBusy(string $name): bool
    {
        return $this->cache->contains($this->getCacheName($name));
    }

    public function hold(string $name): void
    {
        $this->cache->save($this->getCacheName($name), '1', $this->maxTime);
    }

    public function release(string $name): void
    {
        $this->cache->delete($this->getCacheName($name));
    }

    public function setNamespace(string $namespace): void
    {
        $this->namespace = $namespace;
    }

    public function setMaxTime(int $seconds): void
    {
        $this->maxTime = $seconds;
    }

    private function getCacheName(string $name): string
    {
        return $this->namespace . $name;
    }

}
