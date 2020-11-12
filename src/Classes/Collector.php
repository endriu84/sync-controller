<?php

namespace PandaSoft\Sync\Classes;

class Collector
{
    /** @var int[] */
    private $incrementData = [];
    /** @var int[] */
    private $gaugeData = [];
    /** @var int[] */
    private $timingData = [];

    /**
     * Updates a counter by some arbitrary amount.
     *
     * @param string $variable
     * @param int    $value    The amount to increment the counter by
     */
    public function measure($variable, $value)
    {
        if (!isset($this->incrementData[$variable])) {
            $this->incrementData[$variable] = 0;
        }
        $this->incrementData[$variable] += $value;
    }

    /**
     * Increments a counter.
     *
     * @param string $variable
     */
    public function increment($variable)
    {
        $this->measure($variable, 1);
    }

    /**
     * Decrements a counter.
     *
     * @param string $variable
     */
    public function decrement($variable)
    {
        $this->measure($variable, -1);
    }

    public function start()
    {
        $this->timing('start_time', \microtime(true));
        $this->gauge('start_memory', \memory_get_usage());
    }

    public function stop()
    {
        if ($start_time = $this->getTiming('start_time')) {
            $this->timing('execution_time', \microtime(true) - $start_time);
        }
        if ($start_memory = $this->getGauge('start_memory')) {
            $this->gauge('memory_used', \memory_get_usage() - $start_memory);
        }
    }

    /**
     * Records a timing.
     *
     * @param string $variable
     * @param int    $time     The duration of the timing in milliseconds
     */
    public function timing($variable, $time)
    {
        if (!isset($this->timingData[$variable])) {
            $this->timingData[$variable] = 0;
        }
        $this->timingData[$variable] = $time;
    }

    /**
     * Sends the metrics to the adapter backend.
     */
    public function flush()
    {
        $this->timingData = [];
        $this->gaugeData = [];
        $this->incrementData = [];
    }

    /**
     * Updates a gauge by an arbitrary amount.
     *
     * @param string $variable
     * @param int    $value
     */
    public function gauge($variable, $value)
    {
        $sign = substr($value, 0, 1);

        if (in_array($sign, ['-', '+'])) {
            $this->gaugeIncrement($variable, (int) $value);

            return;
        }

        $this->gaugeData[$variable] = $value;
    }

    /**
     * Returns current value of incremented/decremented/measured variable.
     *
     * @param string $variable
     *
     * @return int
     */
    public function getMeasure($variable)
    {
        return isset($this->incrementData[$variable]) ? $this->incrementData[$variable] : 0;
    }

    /**
     * Returns current value of gauged variable.
     *
     * @param string $variable
     *
     * @return int
     */
    public function getGauge($variable)
    {
        return isset($this->gaugeData[$variable]) ? $this->gaugeData[$variable] : 0;
    }

    /**
     * Returns current value of timed variable.
     *
     * @param string $variable
     *
     * @return int
     */
    public function getTiming($variable)
    {
        return isset($this->timingData[$variable]) ? $this->timingData[$variable] : 0;
    }

    /**
     * @param string $variable
     * @param int    $value
     */
    private function gaugeIncrement($variable, $value)
    {
        if (!isset($this->gaugeData[$variable])) {
            $this->gaugeData[$variable] = 0;
        }

        $this->gaugeData[$variable] += $value;
    }

    /**
     * Basic __toString method
     * Mostly should be override
     *
     * @return string
     */
    public function __toString(): string
    {
        $czas_wykonania = gmdate("H:i:s", $this->getTiming('execution_time'));
        $summary =  "SUMMARY\n";
        $summary .= "Execution time: {$czas_wykonania} h\n";
        $summary .= "Memory used: " . \round($this->getGauge('memory_used') / 1048576, 2) . " MB\n";

        return $summary;
    }
}
