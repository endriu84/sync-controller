<?php

namespace PandaSoft\Sync;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use ReflectionClass;
use Exception;
use PandaSoft\Sync\Exception\SyncException;
use PandaSoft\Sync\Exception\SingleRunException;
use PandaSoft\Sync\Classes\Collector;
use PandaSoft\Sync\Classes\Setup;
use PandaSoft\Sync\DataProviderInterface;

class SyncController implements LoggerAwareInterface
{
    /**
     * @var DataProviderInterface
     */
    public $dataProvider;

    /**
     * @var Collector
     */
    public $collector;

    /**
     * @var LoggerInterface
     */
    public $logger;

    /**
     * @var Setup
     */
    public $setup;

    /**
     * @var array SyncInterface
     */
    private $decorators = [];

    /**
     * Constructor
     */
    public function __construct(
        DataProviderInterface $dataProvider
    ) {
        $this->dataProvider = $dataProvider;
        $this->collector = new Collector();
        $this->logger = new NullLogger();
        $this->setup = new Setup();
    }

    public function run()
    {
        try {
            $this->logger->info('Start');
            $this->collector->start();
            $this->setup->run();

            if (empty($this->decorators)) {
                throw new SyncException("You did not set any class to run against provided data - use ->setDecorators() method");
            }

            while ($this->dataProvider->read()) {
                try {
                    $sync = null;
                    foreach ($this->decorators as $k => $className) {
                        if ($k === 0) {
                            $sync = new $className($this);
                        } else {
                            $sync = new $className($sync);
                        }
                    }
                    $sync->run();
                    unset($sync);
                } catch (SingleRunException $e) {
                    // TODO maybe start transaction / rollback here
                    $this->logger->warning($e->getMessage(), [
                        'e' => $e
                    ]);
                }
            }
        } catch (SyncException $e) {
            $this->logger->error($e->getMessage(), [
                'e' => $e
            ]);
        }

        $this->collector->stop();
        $this->logger->notice($this->collector);
    }

    /**
     * Set the setup object
     *
     * @param Setup $setup
     */
    public function setSetup(Setup $setup)
    {
        $this->setup = $setup;
    }

    /**
     * Set the logger to use to log debugging data.
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Set the collector to collect syncronization data and metrics
     *
     * @param Collector $collector
     */
    public function setCollector(Collector $collector)
    {
        $this->collector = $collector;
    }

    /**
     * Sets Decorator classes used to run integration
     *
     * @param array $decorators
     * @return void
     */
    public function setDecorators(array $decorators)
    {
        foreach ($decorators as $k => $className) {
            $class = new ReflectionClass($className);
            if (!$class->implementsInterface('\PandaSoft\SC\SyncInterface')) {
                throw new Exception("Decorator class must implement SyncInterface");
            }
            if ($k === 0 && !$class->isSubclassOf('\PandaSoft\SC\SyncBase')) {
                throw new Exception("First decorator class must extend SyncBase class");
            }
            unset($class); // TODO is it neccessery?
            $this->decorators[] = $className;
        }
    }
}
