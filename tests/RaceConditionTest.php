<?php

namespace tests;

use Exception;
use PHPUnit\Framework\TestCase;
use PreventRaceCondition\RaceCondition;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

class RaceConditionTest extends TestCase
{
  
    public function getPSR16Cache(): Psr16Cache
    {
        $psr6Cache = new FilesystemAdapter();
        return new Psr16Cache($psr6Cache);
    }
    /**
     * @throws Exception
     */
    public function testShouldHoldAndReturnBusy(): void
    {
        $raceCondition = new RaceCondition($this->getPSR16Cache());

        $someName = 'name' . random_int(0, 1000000);

        $raceCondition->lock($someName);

        $this->assertTrue($raceCondition->isBusy($someName));

        $raceCondition->release($someName);

        $this->assertFalse($raceCondition->isBusy($someName));
    }

    /**
     * @throws Exception
     */
    public function testShouldReturnNotBusy(): void
    {
        $raceCondition = new RaceCondition($this->getPSR16Cache());

        $someName = 'name' . random_int(0, 1000000);

        $this->assertFalse($raceCondition->isBusy($someName));
    }

    /**
     * @throws Exception
     */
    public function testShouldHoldAndReturnNotBusyAfterTime(): void
    {
        $raceCondition = new RaceCondition($this->getPSR16Cache());

        $someName = 'name' . random_int(0, 1000000);
        $time = 1;

        $raceCondition->setMaxTime($time);
        $raceCondition->lock($someName);

        $this->assertTrue($raceCondition->isBusy($someName));

        $sleepTime = (int)(1.1 * 10 ** 6); // 1.1 second
        usleep($sleepTime);

        $this->assertFalse($raceCondition->isBusy($someName));
    }

    /**
     * @throws Exception
     */
    public function testChangeNamespace(): void
    {
        $raceCondition = new RaceCondition($this->getPSR16Cache());

        $someName = 'name' . random_int(0, 1000000);
        $somePrefix = 'namespace' . random_int(0, 10000);

        $raceCondition->setPrefix($somePrefix);
        $raceCondition->lock($someName);

        $this->assertTrue($raceCondition->isBusy($someName));

        $raceCondition->setPrefix('other_prefix');

        $this->assertFalse($raceCondition->isBusy($someName));
    }
}
