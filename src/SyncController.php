<?php

namespace PandaSoft\Sync;

use Exception;
use ReflectionClass;
use Psr\Log\NullLogger;
use PandaSoft\Sync\NullSync;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;

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
     * @var string
     */
    public $entityClass;

    /**
     * @var array SyncInterface
     */
    private $decoratorClass = [];

    /**
     * Constructor
     */
    public function __construct(
        DataProviderInterface $dataProvider
    ) {
        $this->dataProvider = $dataProvider;
        $this->collector = new Collector();
        $this->logger = new NullLogger();
    }

    public function run()
    {
        try {
            $this->logger->info('Start');
            $this->collector->start();

            while ($this->dataProvider->read()) {
                try {
                    $entityClass = $this->entityClass;
                    $component = new $entityClass($this);

                    if (!empty($this->decoratorClass)) {
                        foreach ($this->decoratorClass as $className) {
                            $component = new $className($component);
                        }
                    }
                    $component->run();
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
    public function setDecoratorClass(array $decorators): void
    {
        foreach ($decorators as $k => $className) {
            $class = new ReflectionClass($className);
            if (!$class->isSubclassOf('\PandaSoft\Sync\AbstractDecorator')) {
                throw new Exception("Every decorator class must extend AbstractDecorator class");
            }
            $this->decoratorClass[] = $className;
        }
    }

    /**
     * Sets Main component class (like, product, category, order etc)
     *
     * @param string $className
     * @return void
     */
    public function setEntityClass(string $className): void
    {
        $class = new ReflectionClass($className);
        if (!$class->implementsInterface('\PandaSoft\Sync\SyncInterface')) {
            throw new Exception("Component class must implement SyncInterface");
        }

        $this->entityClass = $className;
    }
}
