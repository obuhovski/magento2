<?php

namespace Ewave\BannerStaging\Model;

use Ewave\BannerStaging\Api\Data\BannerInterface;
use Ewave\BannerStaging\Model\ResourceModel\BannerSegmentLink;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Banner
 */
class Banner extends \Magento\Banner\Model\Banner implements BannerInterface
{
    /**
     * @var BannerSegmentLink
     */
    private $bannerSegmentLink;

    /**
     * Banner constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param BannerSegmentLink $bannerSegmentLink
     * @param ResourceModel\Banner|null $resource
     * @param ResourceModel\Banner\Collection|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        BannerSegmentLink $bannerSegmentLink,
        \Ewave\BannerStaging\Model\ResourceModel\Banner $resource = null,
        \Ewave\BannerStaging\Model\ResourceModel\Banner\Collection $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->bannerSegmentLink = $bannerSegmentLink;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return parent::getData(BannerInterface::BANNER_ID);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->getData(BannerInterface::NAME);
    }

    /**
     * @inheritdoc
     */
    public function getIsEnabled()
    {
        return $this->getData(BannerInterface::IS_ENABLED);
    }

    /**
     * @inheritdoc
     */
    public function getIsGaEnabled()
    {
        return $this->getData(BannerInterface::IS_GA_ENABLED);
    }

    /**
     * @return string|array
     */
    public function getTypes()
    {
        $types = $this->_getData(BannerInterface::TYPES);
        if (is_array($types)) {
            return $types;
        }
        if (empty($types)) {
            $types = [];
        } else {
            $types = explode(',', $types);
        }
        $this->setData(BannerInterface::TYPES, $types);
        return $types;
    }

    /**
     * @inheritdoc
     */
    public function getGaCreative()
    {
        return $this->getData(BannerInterface::GA_CREATIVE);
    }

    /**
     * @inheritdoc
     */
    public function getCreatedIn()
    {
        return $this->getData(BannerInterface::CREATED_IN);
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedIn()
    {
        return $this->getData(BannerInterface::UPDATED_IN);
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        return $this->setData(BannerInterface::NAME, $name);
    }

    /**
     * @inheritdoc
     */
    public function setIsEnabled($isEnabled)
    {
        return $this->setData(BannerInterface::IS_ENABLED, $isEnabled);
    }

    /**
     * @inheritdoc
     */
    public function setTypes($types)
    {
        return $this->setData(BannerInterface::TYPES, $types);
    }

    /**
     * @inheritdoc
     */
    public function setIsGaEnabled($isGaEnabled)
    {
        return $this->setData(BannerInterface::IS_GA_ENABLED, $isGaEnabled);
    }

    /**
     * @inheritdoc
     */
    public function setGaCreative($gaCreative)
    {
        return $this->setData(BannerInterface::GA_CREATIVE, $gaCreative);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedIn($createdIn)
    {
        return $this->setData(BannerInterface::CREATED_IN, $createdIn);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedIn($updatedIn)
    {
        return $this->setData(BannerInterface::UPDATED_IN, $updatedIn);
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        return $this->setData(BannerInterface::BANNER_ID, $id);
    }

    /**
     * Get row ID
     *
     * @return int|null
     */
    public function getRowId()
    {
        return $this->getData(BannerInterface::ROW_ID);
    }

    /**
     * @param int $rowId
     * @return $this
     */
    public function setRowId($rowId)
    {
        return $this->setData(BannerInterface::ROW_ID, $rowId);
    }

    /**
     * Save banner content, bind banner to catalog and sales rules after banner save
     *
     * @return $this
     */
    public function afterSave()
    {
        if ($this->hasStoreContents()) {
            $this->_getResource()->saveStoreContents(
                $this->getRowId(),
                $this->getStoreContents(),
                $this->getStoreContentsNotUse()
            );
        }
        if ($this->hasBannerCatalogRules()) {
            $this->_getResource()->saveCatalogRules($this->getId(), $this->getBannerCatalogRules());
        }
        if ($this->hasBannerSalesRules()) {
            $this->_getResource()->saveSalesRules($this->getId(), $this->getBannerSalesRules());
        }
        return AbstractModel::afterSave();
    }

    /**
     * Get all existing banner contents
     *
     * @return array|null
     */
    public function getStoreContents()
    {
        if (!$this->hasStoreContents()) {
            $contents = $this->_getResource()->getStoreContents($this->getRowId());
            $this->setStoreContents($contents);
        }
        return $this->_getData(BannerInterface::STORE_CONTENTS);
    }

    /**
     * Retrieve array of sales rules id's for banner
     *
     * @return array
     */
    public function getRelatedSalesRule()
    {
        if (!$this->getRowId()) {
            return [];
        }
        $array = $this->getData(BannerInterface::RELATED_SALES_RULE);
        if ($array === null) {
            $array = $this->getResource()->getRelatedSalesRule($this->getRowId());
            $this->setData(BannerInterface::RELATED_SALES_RULE, $array);
        }
        return $array;
    }

    /**
     * Retrieve array of catalog rules id's for banner
     *
     * @return array
     */
    public function getRelatedCatalogRule()
    {
        if (!$this->getRowId()) {
            return [];
        }
        $array = $this->getData(BannerInterface::RELATED_CATALOG_RULE);
        if ($array === null) {
            $array = $this->getResource()->getRelatedCatalogRule($this->getRowId());
            $this->setData(BannerInterface::RELATED_CATALOG_RULE, $array);
        }
        return $array;
    }

    /**
     * @return $this
     */
    protected function _afterLoad()
    {
        $this->getStoreContents();

        $segmentIds = $this->bannerSegmentLink->loadBannerSegments($this->getRowId());
        $this->setData(BannerInterface::CUSTOMER_SEGMENT_IDS, $segmentIds);

        if (!empty($this->getData(BannerInterface::TYPES))) {
            $this->setData(BannerInterface::APPLIES_TO, 1);
        }

        if (!empty($this->getData(BannerInterface::CUSTOMER_SEGMENT_IDS))) {
            $this->setData(BannerInterface::CUSTOMER_SEGMENTS, 1);
        }

        return parent::_afterLoad();
    }
}
