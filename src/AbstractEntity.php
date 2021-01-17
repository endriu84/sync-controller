<?php

namespace PandaSoft\Sync;

use Psr\Log\LoggerInterface;
use PandaSoft\Sync\CollectorInterface;

abstract class AbstractEntity implements SyncInterface
{
    /**
     * ID of a entity (product)
     *
     * @var boolean
     */
    protected $id = 0;

    /**
     * @var SyncController
     */
    protected $sc;

    /**
     * Constructor
     *
     * @param SyncController $sc
     */
    public function __construct(SyncController $sc)
    {
        $this->sc = $sc;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    abstract public function run(): void;

    /**
     * Undocumented function
     *
     * @return LoggerInterface
     */
    public function log(): LoggerInterface
    {
        return $this->sc->logger;
    }

    /**
     * Undocumented function
     *
     * @return CollectorInterface
     */
    public function collector(): CollectorInterface
    {
        return $this->sc->collector;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getData(): object
    {
        return $this->sc->dataProvider->getData();
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function supplierName(): string
    {
        return $this->sc->dataProvider->supplierName();
    }

    /**
     * Get Entity object ID
     *
     * @return integer
     */
    public function id(): int
    {
        return $this->id;
    }
}
