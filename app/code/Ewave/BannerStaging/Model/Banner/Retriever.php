<?php
namespace Ewave\BannerStaging\Model\Banner;

use Ewave\BannerStaging\Model\Banner;
use Ewave\BannerStaging\Model\BannerFactory;
use Magento\Framework\DataObject;
use Magento\Staging\Model\Entity\RetrieverInterface;

class Retriever implements RetrieverInterface
{
    /**
     * @var BannerFactory
     */
    protected $bannerFactory;

    /**
     * Retriever constructor.
     * @param BannerFactory $bannerFactory
     */
    public function __construct(
        BannerFactory $bannerFactory
    ) {
        $this->bannerFactory = $bannerFactory;
    }

    /**
     * @param string $entityId
     * @return Banner
     */
    public function getEntity($entityId)
    {
        /** @var Banner $entity */
        $entity = $this->bannerFactory->create();
        $entity->load($entityId);
        return $entity;
    }
}
