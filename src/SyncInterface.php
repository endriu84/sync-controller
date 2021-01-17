<?php

namespace PandaSoft\Sync;

use Psr\Log\LoggerInterface;
use PandaSoft\Sync\CollectorInterface;

interface SyncInterface
{
    public function run(): void;

    public function log(): LoggerInterface;

    public function collector(): CollectorInterface;

    public function getData(): object;

    public function id(): int;
    
    public function supplierName(): string;
}
