<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Model\ResourceModel\Report;

/**
 * Report resource collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Set model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Support\Model\Report', 'Magento\Support\Model\ResourceModel\Report');
    }
}
