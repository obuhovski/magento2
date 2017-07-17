<?php
namespace Ewave\BannerStaging\Observer;

use Ewave\BannerStaging\Api\BannerRepositoryInterface;
use Magento\Banner\Model\ResourceModel\BannerFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Event\ObserverInterface;
use Magento\Banner\Observer\BindRelatedBannersToCatalogRule as BannerBindRelatedBannersToCatalogRule;

class BindRelatedBannersToCatalogRule extends BannerBindRelatedBannersToCatalogRule implements ObserverInterface
{
    /**
     * Banner factory
     *
     * @var BannerFactory
     */
    protected $_bannerFactory = null;

    /**
     * @var BannerRepositoryInterface
     */
    protected $bannerRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @param BannerFactory $bannerFactory
     * @param BannerRepositoryInterface $bannerRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        BannerFactory $bannerFactory,
        BannerRepositoryInterface $bannerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->_bannerFactory = $bannerFactory;
        $this->bannerRepository = $bannerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Bind specified banners to catalog rule
     *
     * @param   \Magento\Framework\Event\Observer $observer
     * @return  \Magento\Banner\Observer\BindRelatedBannersToCatalogRule
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $catalogRule = $observer->getEvent()->getRule();
        $banners = $catalogRule->getRelatedBanners();
        if (empty($banners)) {
            $banners = [];
        }

        $criteria = $this->searchCriteriaBuilder->addFilter('banner_id', $banners, 'in')->create();
        $banners = $this->bannerRepository->getList($criteria);
        $bannersRowIds = array_map(function ($item) {
            return $item->getRowId();
        }, $banners->getItems());

        $this->_bannerFactory->create()->bindBannersToCatalogRule($catalogRule->getId(), $bannersRowIds);

        return $this;
    }
}
