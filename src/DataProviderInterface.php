<?php

namespace PandaSoft\Sync;

interface DataProviderInterface
{
    public function read(): bool;

    public function getData();
}
