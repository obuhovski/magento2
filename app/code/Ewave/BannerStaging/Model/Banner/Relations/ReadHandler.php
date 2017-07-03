<?php
namespace Ewave\BannerStaging\Model\Banner\Relations;

use Magento\Banner\Model\ResourceModel\Banner;
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
     * SaveHandler constructor.
     * @param MetadataPool $metadataPool
     * @param Banner $resourceBanner
     */
    public function __construct(
        MetadataPool $metadataPool,
        Banner $resourceBanner
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceBanner = $resourceBanner;
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
        return $entity;
    }
}
