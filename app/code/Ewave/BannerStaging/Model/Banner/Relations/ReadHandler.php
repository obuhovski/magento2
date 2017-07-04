<?php
namespace Ewave\BannerStaging\Model\Banner\Relations;

use Magento\Banner\Model\ResourceModel\Banner;
use Magento\BannerCustomerSegment\Model\ResourceModel\BannerSegmentLink;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Cms\Model\ResourceModel\Block;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * Class SaveHandler
 */
class ReadHandler implements ExtensionInterface
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
     * SaveHandler constructor.
     * @param MetadataPool $metadataPool
     * @param Banner $resourceBanner
     * @param BannerSegmentLink $bannerSegmentLink
     */
    public function __construct(
        MetadataPool $metadataPool,
        Banner $resourceBanner,
        BannerSegmentLink $bannerSegmentLink
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceBanner = $resourceBanner;
        $this->bannerSegmentLink = $bannerSegmentLink;
    }

    /**
     * @param \Magento\Banner\Model\Banner $entity
     * @param array $arguments
     * @return object
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        $entity->getStoreContents();

        $segmentIds = $this->bannerSegmentLink->loadBannerSegments($entity->getRowId());
        $entity->setData('customer_segment_ids', $segmentIds);

        return $entity;
    }
}
