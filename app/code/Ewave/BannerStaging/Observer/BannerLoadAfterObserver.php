<?php
namespace Ewave\BannerStaging\Observer;

use Magento\Banner\Model\Banner;
use Magento\Framework\Event\ObserverInterface;

class BannerLoadAfterObserver implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var Banner $entity */
//        $entity = $observer->getEvent()->getEntity();
//        $entity->getStoreContents();
        return $this;
    }
}
