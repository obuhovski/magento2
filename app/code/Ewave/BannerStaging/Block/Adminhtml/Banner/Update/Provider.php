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
     * @var UrlProviderInterface
     */
    protected $previewProvider;

    /**
     * @param RequestInterface $request
     * @param BannerRepositoryInterface $bannerRepository
     * @param VersionManager $versionManager
     * @param UrlProviderInterface $previewProvider
     */
    public function __construct(
        RequestInterface $request,
        BannerRepositoryInterface $bannerRepository,
        VersionManager $versionManager,
        UrlProviderInterface $previewProvider
    ) {
        $this->request = $request;
        $this->bannerRepository = $bannerRepository;
        $this->versionManager = $versionManager;
        $this->previewProvider = $previewProvider;
    }

    /**
     * Return Banner by request
     *
     * @return BannerInterface
     */
    protected function getBanner()
    {
        return $this->bannerRepository->getById($this->request->getParam('id'));
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
            $oldUpdateId = $this->versionManager->getCurrentVersion()->getId();
            $this->versionManager->setCurrentVersionId($updateId);
            $url = $this->previewProvider->getUrl($this->getBanner()->getData());
            $this->versionManager->setCurrentVersionId($oldUpdateId);
            return $url;
    }
}
