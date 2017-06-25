<?php
/**
 * Reward History grid
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Block\Adminhtml\Customer\Edit\Tab\Reward\History;

/**
 * @codeCoverageIgnore
 */
class Grid extends \Magento\Backend\Block\Widget\Grid
{
    /**
     * Prepare grid collection object
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $customerId = $this->getRequest()->getParam('id', 0);
        $this->getCollection()->addCustomerFilter($customerId);
        return parent::_prepareCollection();
    }
}
