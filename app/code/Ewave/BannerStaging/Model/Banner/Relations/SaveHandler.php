<?php
namespace Ewave\BannerStaging\Model\Banner\Relations;

use Magento\Banner\Model\ResourceModel\BannerFactory;
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
     * SaveHandler constructor.
     * @param MetadataPool $metadataPool
     * @param BannerFactory $resourceBanner
     */
    public function __construct(
        MetadataPool $metadataPool,
        BannerFactory $resourceBanner
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceBanner = $resourceBanner->create();
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

        return $entity;
    }
}
