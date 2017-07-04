<?php
namespace Ewave\BannerStaging\Model\Banner\Relations;

use Magento\Banner\Model\ResourceModel\BannerFactory;
use Magento\BannerCustomerSegment\Model\ResourceModel\BannerSegmentLink;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Cms\Model\ResourceModel\Block;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * Class SaveHandler
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var Block
     */
    protected $resourceBanner;
    /**
     * @var BannerSegmentLink
     */
    private $bannerSegmentLink;
    /**
     * @var \Magento\CustomerSegment\Helper\Data
     */
    private $segmentHelper;

    /**
     * SaveHandler constructor.
     * @param MetadataPool $metadataPool
     * @param BannerFactory $resourceBanner
     * @param BannerSegmentLink $bannerSegmentLink
     * @param \Magento\CustomerSegment\Helper\Data $segmentHelper
     */
    public function __construct(
        MetadataPool $metadataPool,
        BannerFactory $resourceBanner,
        BannerSegmentLink $bannerSegmentLink,
        \Magento\CustomerSegment\Helper\Data $segmentHelper
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceBanner = $resourceBanner->create();
        $this->bannerSegmentLink = $bannerSegmentLink;
        $this->segmentHelper = $segmentHelper;
    }

    /**
     * @param \Magento\Banner\Model\Banner $entity
     * @param array $arguments
     * @return object
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        $resourceBanner = $this->resourceBanner;

        if ($entity->hasStoreContents()) {
            $resourceBanner->saveStoreContents(
                $entity->getRowId(),
                $entity->getStoreContents(),
                $entity->getStoreContentsNotUse()
            );
        }
        if ($entity->hasBannerCatalogRules()) {
            $resourceBanner->saveCatalogRules($entity->getRowId(), $entity->getBannerCatalogRules());
        }

        if ($entity->hasBannerSalesRules()) {
            $resourceBanner->saveSalesRules($entity->getRowId(), $entity->getBannerSalesRules());
        }


        if ($this->segmentHelper->isEnabled()) {
            $segmentIds = $entity->getData('customer_segment_ids') ?: [];
            $segmentIds = array_map('intval', $segmentIds);
            $this->bannerSegmentLink->saveBannerSegments($entity->getRowId(), $segmentIds);
        }

        return $entity;
    }
}
