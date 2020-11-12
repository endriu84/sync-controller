<?php

namespace PandaSoft\Sync;

use PandaSoft\Sync\SyncController;

abstract class SyncBase
{
    /**
     * ID of a entity (product)
     *
     * @var boolean
     */
    protected $id = false;

    /**
     * Sync controller
     *
     * @var SyncController
     */
    protected $sc;

    public function __construct(SyncController $sc)
    {
        $this->sc = $sc;
    }
}
