<?php

namespace Ewave\BannerStaging\Model;

use Ewave\BannerStaging\Api\Data\BannerInterface;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Model\AbstractModel;

/**
 * Class PageApplier
 */
class Banner extends \Magento\Banner\Model\Banner implements BannerInterface
{
    public function getName()
    {
        return $this->getData('name');
    }

    public function getIsEnabled()
    {
        return $this->getData('is_enabled');
    }

    public function etIsGaCreative()
    {
        return $this->getData('is_enabled');
    }

    public function getCreatedIn()
    {
        return $this->getData('is_enabled');
    }

    public function getUpdatedIn()
    {
        return $this->getData('is_enabled');
    }

    public function setName($name)
    {
        return $this->setName($name);
    }

    public function setIsEnabled($isEnabled)
    {
        return $this->setName($name);
    }

    public function setTypes($types)
    {
        return $this->setName($name);
    }

    public function setIsGaCreative($isGaCreative)
    {
        return $this->setName($name);
    }

    public function setCreatedIn($createdIn)
    {
        return $this->setName($name);
    }

    public function setUpdatedIn($updatedIn)
    {
        return $this->setName($name);
    }
}
