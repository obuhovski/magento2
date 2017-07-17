<?php
namespace Ewave\BannerStaging\Observer;

use Ewave\BannerStaging\Api\Data\BannerInterface;
use Magento\Framework\Event\ObserverInterface;

class BannerSaveBeforeObserver implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var BannerInterface $entity */
        $entity = $observer->getEvent()->getEntity();
        $entity->beforeSave();
        $types = $entity->getTypes();
        if (empty($types)) {
            $types = null;
        } elseif (is_array($types)) {
            $types = implode(',', $types);
        }
        $entity->setTypes($types);
        return $this;
    }
}
