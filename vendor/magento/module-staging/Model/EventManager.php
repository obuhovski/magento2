<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Model;

class EventManager
{
    /**
     * @var \Magento\Staging\Model\VersionManager
     */
    protected $versionManager;

    /**
     * EventManager constructor.
     * @param VersionManager $versionManager
     */
    public function __construct(
        \Magento\Staging\Model\VersionManager $versionManager
    ) {
        $this->versionManager = $versionManager;
    }
}
