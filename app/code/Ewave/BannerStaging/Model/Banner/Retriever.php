<?php
namespace Ewave\BannerStaging\Model\Banner;

use Ewave\BannerStaging\Api\BannerRepositoryInterface;
use Ewave\BannerStaging\Api\Data\BannerInterface;
use Ewave\BannerStaging\Model\BannerFactory;
use Magento\Staging\Model\Entity\RetrieverInterface;

class Retriever implements RetrieverInterface
{
    /**
     * @var BannerFactory
     */
    protected $bannerRepository;

    /**
     * Retriever constructor.
     * @param BannerRepositoryInterface $bannerRepository
     */
    public function __construct(
        BannerRepositoryInterface $bannerRepository
    ) {
        $this->bannerRepository = $bannerRepository;
    }

    /**
     * @param string $entityId
     * @return BannerInterface
     */
    public function getEntity($entityId)
    {
        return $this->bannerRepository->getById($entityId);
    }
}
