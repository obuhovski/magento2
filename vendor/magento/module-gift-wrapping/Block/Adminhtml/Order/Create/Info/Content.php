<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Gift wrapping order create info content block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftWrapping\Block\Adminhtml\Order\Create\Info;

use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Content extends \Magento\GiftWrapping\Block\Adminhtml\Order\Create\Info
{
    /**
     * @var \Magento\GiftMessage\Helper\Message
     */
    protected $messageHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\GiftWrapping\Helper\Data $giftWrappingData
     * @param \Magento\GiftWrapping\Model\ResourceModel\Wrapping\CollectionFactory $wrappingCollectionFactory
     * @param \Magento\GiftMessage\Helper\Message $messageHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        \Magento\GiftWrapping\Helper\Data $giftWrappingData,
        \Magento\GiftWrapping\Model\ResourceModel\Wrapping\CollectionFactory $wrappingCollectionFactory,
        \Magento\GiftMessage\Helper\Message $messageHelper,
        array $data = []
    ) {
        $this->messageHelper = $messageHelper;
        parent::__construct(
            $context,
            $sessionQuote,
            $orderCreate,
            $priceCurrency,
            $giftWrappingData,
            $wrappingCollectionFactory,
            $data
        );
    }

    /**
     * @return $this|void
     */
    protected function _beforeToHtml()
    {
        if (!$this->messageHelper->isMessagesAllowed('main', $this->getQuote(), $this->getStoreId())) {
            $this->_template = 'order/create/info/content.phtml';
        }
    }
}
