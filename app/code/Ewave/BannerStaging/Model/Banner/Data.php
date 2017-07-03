<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 2.7.17
 * Time: 14.10
 */

namespace Ewave\BannerStaging\Model\Banner;


use Ewave\BannerStaging\Model\BannerRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;

class Data extends \Magento\Banner\Model\Banner\Data
{
    /**
     * @var BannerRepository
     */
    private $bannerRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * Data constructor.
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Banner\Model\ResourceModel\Banner $bannerResource
     * @param \Magento\Banner\Model\Banner $banner
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param BannerRepository $bannerRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Banner\Model\ResourceModel\Banner $bannerResource,
        \Magento\Banner\Model\Banner $banner,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        BannerRepository $bannerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        parent::__construct($checkoutSession, $bannerResource, $banner, $storeManager, $httpContext, $filterProvider);
        $this->bannerRepository = $bannerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    protected function getFixedBanners()
    {
        $criteria = $this->searchCriteriaBuilder->addFilter('is_enabled', 1)->create();
        $banners = $this->bannerRepository->getList($criteria);
        $bannerArray = [];
        foreach ($banners as $banner) {
            $content = $banner->getResource()->getStoreContent($banner->getRowId(), $this->storeId);
            $bannerArray[$banner->getId()] = [
                'content' => $this->filterProvider->getPageFilter()->filter($content),
                'types' => $banner->getTypes(),
                'id' => $banner->getId(),
            ];
        }
        return array_filter($bannerArray);
    }
}