<?php

namespace PandaSoft\Sync;

interface DataProviderInterface
{
    public function read(): bool;

    public function getData();

    public function supplierName(): string;
}
