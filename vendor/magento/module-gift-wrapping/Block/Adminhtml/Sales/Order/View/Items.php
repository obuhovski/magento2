<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GiftWrapping\Block\Adminhtml\Sales\Order\View;

/**
 * Gift wrapping adminhtml block for view order items
 */
class Items extends \Magento\Sales\Block\Adminhtml\Items\AbstractItems
{
    /**
     * Get order item from parent block
     *
     * @return \Magento\Sales\Model\Order\Item
     * @codeCoverageIgnore
     */
    public function getItem()
    {
        return $this->getParentBlock()->getData('item');
    }

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getItem() && $this->getItem()->getGwId()) {
            return parent::_toHtml();
        } else {
            return false;
        }
    }
}
