<?php

namespace Ewave\BannerStaging\Block\Adminhtml\Banner\Update;

use Ewave\BannerStaging\Api\Data\BannerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Ewave\BannerStaging\Api\BannerRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Staging\Block\Adminhtml\Update\Entity\EntityProviderInterface;
use Magento\Staging\Model\VersionManager;
use Magento\Staging\Ui\Component\Listing\Column\Entity\UrlProviderInterface;

/**
 * Class GenericButton
 */
class Provider implements EntityProviderInterface
{
    /**
     * @var BannerRepositoryInterface
     */
    protected $bannerRepository;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var VersionManager
     */
    protected $versionManager;


    /**
     * @param RequestInterface $request
     * @param BannerRepositoryInterface $bannerRepository
     * @param VersionManager $versionManager
     */
    public function __construct(
        RequestInterface $request,
        BannerRepositoryInterface $bannerRepository,
        VersionManager $versionManager
    ) {
        $this->request = $request;
        $this->bannerRepository = $bannerRepository;
        $this->versionManager = $versionManager;
    }

    /**
     * Return Banner by request
     *
     * @return BannerInterface
     */
    protected function getBanner()
    {
        $id = $this->request->getParam('id') ?: $this->request->getParam('banner_id');
        return $this->bannerRepository->getById($id);
    }

    /**
     * Return Banner ID
     *
     * @return int|null
     */
    public function getId()
    {
        try {
            return $this->getBanner()->getId();
        } catch (NoSuchEntityException $e) {

        }
        return null;
    }

    /**
     * Return Banner Url
     *
     * @param int $updateId
     * @return null|string
     */
    public function getUrl($updateId)
    {
        return null;
    }
}
