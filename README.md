# Prevent Race Condition
[![codecov](https://codecov.io/gh/antonsacred/matrix-determinant/branch/master/graph/badge.svg?token=C10CHOFXWE)](https://codecov.io/gh/antonsacred/prevent-race-condition)
### Basic usage

You need PSR-6 or PSR-16 cache to use this

```php
use PreventRaceCondition\RaceConditionFactory;

// $somePSR16Cache is PSR16 any cache instance 
$raceCondition = RaceConditionFactory::fromCacheItemPool($somePSR16Cache);

// or

// $somePSR6Cache is PSR6 any cache instance 
$raceCondition = RaceConditionFactory::fromSimpleCache($somePSR6Cache);


while($raceCondition->isBusy('lock-name')) {
    // wait or do something else
}

$raceCondition->lock('lock-name');

// do something

$raceCondition->release('lock-name');
```