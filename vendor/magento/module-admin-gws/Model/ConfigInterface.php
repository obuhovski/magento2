<?php
/**
 * AdminGws configuration model interface
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdminGws\Model;

interface ConfigInterface
{
    /**
     * Get callback list by group name
     *
     * @param string $groupName
     * @return array
     */
    public function getCallbacks($groupName);

    /**
     * Get deny acl level rules
     *
     * @param string $level
     * @return array
     */
    public function getDeniedAclResources($level);

    /**
     * Get group processor
     *
     * @param string $groupName
     * @return string|null
     */
    public function getGroupProcessor($groupName);

    /**
     * Get callback for the object
     *
     * @param object $object
     * @param string $callbackGroup
     * @return string|null
     */
    public function getCallbackForObject($object, $callbackGroup);
}
