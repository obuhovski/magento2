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


    /**
     * @inheritdoc
     */
    public function getId()
    {
        return parent::getData('banner_id');
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->getData('name');
    }

    /**
     * @inheritdoc
     */
    public function getIsEnabled()
    {
        return $this->getData('is_enabled');
    }

    /**
     * @inheritdoc
     */
    public function getIsGaEnabled()
    {
        return $this->getData('is_ga_enabled');
    }

    /**
     * @return string|array
     */
    public function getTypes()
    {
        return $this->getData('types');
    }

    /**
     * @inheritdoc
     */
    public function getGaCreative()
    {
        return $this->getData('ga_creative');
    }

    /**
     * @inheritdoc
     */
    public function getCreatedIn()
    {
        return $this->getData('created_in');
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedIn()
    {
        return $this->getData('updated_in');
    }

    public function setName($name)
    {
        return $this->setData('name', $name);
    }

    public function setIsEnabled($isEnabled)
    {
        return $this->setData('is_enabled', $isEnabled);
    }

    public function setTypes($types)
    {
        return $this->setData('types', $types);
    }

    public function setIsGaEnabled($isGaEnabled)
    {
        return $this->setData('is_ga_enabled', $isGaEnabled);
    }

    public function setGaCreative($gaCreative)
    {
        return $this->setData('ga_creative', $gaCreative);
    }

    public function setCreatedIn($createdIn)
    {
        return $this->setData('created_in', $createdIn);
    }

    public function setUpdatedIn($updatedIn)
    {
        return $this->setData('updated_in', $updatedIn);
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        return $this->setData('banner_id', $id);
    }
}
