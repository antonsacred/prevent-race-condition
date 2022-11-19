<?php
declare(strict_types=1);

namespace PreventRaceCondition;

interface RaceConditionInterface
{
    public function isBusy(string $name): bool;

    public function lock(string $name): void;

    public function release(string $name): void;

    public function setPrefix(string $prefix): void;

    public function setMaxTime(int $seconds): void;
}
