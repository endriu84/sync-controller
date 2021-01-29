<?php

namespace PandaSoft\Sync;

use Psr\Log\LoggerInterface;

abstract class AbstractDecorator implements SyncInterface
{
    /**
     * @var SyncInterface
     */
    protected $sync;

    /**
     * Constructor
     *
     * @param SyncInterface $sync
     */
    public function __construct(SyncInterface $sync)
    {
        $this->sync = $sync;
    }

    public function run(): void
    {
        $this->sync->run();
    }

    public function log(): LoggerInterface
    {
        return $this->sync->log();
    }

    public function collector(): CollectorInterface
    {
        return $this->sync->collector();
    }

    public function getData(): object
    {
        return $this->sync->getData();
    }

    public function id(): int
    {
        return $this->sync->id();
    }

    public function supplierName(): string
    {
        return $this->sync->supplierName();
    }

    public function firstImport(): bool
    {
        return $this->sync->firstImport();
    }
}
