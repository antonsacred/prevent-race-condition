<?php


class RaceConditionTest extends TestCase
{
  
    public function getCache(){
      return null;
    }
    /**
     * @throws Exception
     */
    public function testShouldHoldAndReturnBusy(): void
    {
        $raceCondition = new RaceCondition($this->getCache());

        $someName = 'name' . random_int(0, 1000000);

        $raceCondition->hold($someName);

        $this->assertTrue($raceCondition->isBusy($someName));

        $raceCondition->release($someName);

        $this->assertFalse($raceCondition->isBusy($someName));
    }

    public function testShouldReturnNotBusy(): void
    {
        $raceCondition = new RaceCondition($this->getContainer()->get(Cache::class));

        $someName = 'name' . random_int(0, 1000000);

        $this->assertFalse($raceCondition->isBusy($someName));
    }

    /**
     * @throws Exception
     */
    public function testShouldHoldAndReturnNotBusyAfterTime(): void
    {
        $raceCondition = new RaceCondition($this->getCache());

        $someName = 'name' . random_int(0, 1000000);
        $time = 1;

        $raceCondition->setMaxTime($time);
        $raceCondition->hold($someName);

        $this->assertTrue($raceCondition->isBusy($someName));

        $sleepTime = (int)(1.1 * 10 ** 6);
        usleep($sleepTime);

        $this->assertFalse($raceCondition->isBusy($someName));
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws Exception
     */
    public function testChangeNamespace(): void
    {
        $raceCondition = new RaceCondition($this->getCache());

        $someName = 'name' . random_int(0, 1000000);
        $someNameSpace = 'namespace' . random_int(0, 10000);

        $raceCondition->setNamespace($someNameSpace);
        $raceCondition->hold($someName);

        $this->assertTrue($raceCondition->isBusy($someName));

        $raceCondition->setNamespace('other_namespace');

        $this->assertFalse($raceCondition->isBusy($someName));
    }
}
