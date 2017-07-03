<?php
namespace Ewave\BannerStaging\Observer;

use Ewave\BannerStaging\Model\Banner;
use Magento\Framework\Event\ObserverInterface;

class BannerSaveAfterObserver implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var Banner $entity */
//        $entity = $observer->getEvent()->getEntity();
//        $entity->afterSave();
        return $this;
    }
}
