<?php

namespace PandaSoft\Sync;

interface DataProviderFactoryInterface
{
    public function getDataProvider($entity = 'products'): DataProviderInterface;
}
