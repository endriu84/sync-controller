<?php

namespace PandaSoft\Sync;

interface CollectorInterface
{
    public function measure($variable, $value): void;

    /**
     * Increments a counter.
     *
     * @param string $variable
     */
    public function increment($variable): void;

    /**
     * Decrements a counter.
     *
     * @param string $variable
     */
    public function decrement($variable): void;

    public function start(): void;

    public function stop(): void;

    /**
     * Records a timing.
     *
     * @param string $variable
     * @param int    $time     The duration of the timing in milliseconds
     */
    public function timing($variable, $time): void;

    /**
     * Sends the metrics to the adapter backend.
     */
    public function flush(): void;

    /**
     * Updates a gauge by an arbitrary amount.
     *
     * @param string $variable
     * @param int    $value
     */
    public function gauge($variable, $value): void;

    /**
     * Returns current value of incremented/decremented/measured variable.
     *
     * @param string $variable
     *
     * @return int
     */
    public function getMeasure($variable): int;

    /**
     * Returns current value of gauged variable.
     *
     * @param string $variable
     *
     * @return int
     */
    public function getGauge($variable): int;

    /**
     * Returns current value of timed variable.
     *
     * @param string $variable
     *
     * @return int
     */
    public function getTiming($variable): int;
}
