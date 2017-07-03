<?php
namespace Ewave\BannerStaging\Observer;

use Ewave\BannerStaging\Model\Banner;
use Magento\Framework\Event\ObserverInterface;

class BannerSaveBeforeObserver implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var Banner $entity */
        $entity = $observer->getEvent()->getEntity();
        $entity->beforeSave();
        $types = $entity->getTypes();
        if (empty($types)) {
            $types = null;
        } elseif (is_array($types)) {
            $types = implode(',', $types);
        }
        if (empty($types)) {
            $types = null;
        }
        $entity->setTypes($types);
        return $this;
    }
}
