<?php
namespace Ewave\BannerStaging\Model;

use Ewave\BannerStaging\Api\Data;
use Ewave\BannerStaging\Api\BannerRepositoryInterface;
use Ewave\BannerStaging\Api\Data\BannerInterface;
use Ewave\BannerStaging\Api\Data\BannerSearchResultsInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Banner\Model\BannerFactory;
use Magento\Banner\Model\ResourceModel\Banner as ResourceBanner;
use Magento\Banner\Model\ResourceModel\Banner\CollectionFactory as BannerCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class BannerRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BannerRepository implements BannerRepositoryInterface
{
    /**
     * @var ResourceBanner
     */
    protected $resource;

    /**
     * @var BannerFactory
     */
    protected $bannerFactory;

    /**
     * @var BannerCollectionFactory
     */
    protected $bannerCollectionFactory;

    /**
     * @var Data\BannerSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var Data\BannerInterfaceFactory
     */
    protected $dataBannerFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param ResourceBanner $resource
     * @param BannerFactory $bannerFactory
     * @param Data\BannerInterfaceFactory $dataBannerFactory
     * @param BannerCollectionFactory $bannerCollectionFactory
     * @param Data\BannerSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceBanner $resource,
        BannerFactory $bannerFactory,
        Data\BannerInterfaceFactory $dataBannerFactory,
        BannerCollectionFactory $bannerCollectionFactory,
        Data\BannerSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->bannerFactory = $bannerFactory;
        $this->bannerCollectionFactory = $bannerCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataBannerFactory = $dataBannerFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * Save Banner data
     *
     * @param BannerInterface $banner
     * @return AbstractModel
     * @throws CouldNotSaveException
     */
    public function save(BannerInterface $banner)
    {
        if (empty($banner->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $banner->setStoreId($storeId);
        }
        try {
            $this->resource->save($banner);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the Banner: %1',
                $exception->getMessage()
            ));
        }
        return $banner;
    }

    /**
     * Load Banner data by given Banner Identity
     *
     * @param string $bannerId
     * @return Banner
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($bannerId)
    {
        $banner = $this->bannerFactory->create();
        $this->resource->load($banner, $bannerId);
        if (!$banner->getId()) {
            throw new NoSuchEntityException(__('Banner with id "%1" does not exist.', $bannerId));
        }
        return $banner;
    }

    /**
     * Load Banner data collection by given search criteria
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return BannerSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria = null)
    {
        $collection = $this->bannerCollectionFactory->create();
        $searchResults = $this->searchResultsFactory->create();

        if ($criteria === null) {
            $searchResults->setItems($collection->getItems());
            return $searchResults;
        }
        $searchResults->setSearchCriteria($criteria);

        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * @param array $ids
     * @return \Magento\Framework\DataObject[]
     */
    public function getListByRowIds(array $ids)
    {
        $collection = $this->bannerCollectionFactory->create();
        $collection->addFieldToFilter('row_id IN (?)', $ids);
        return $collection->getItems();
    }

    /**
     * Delete Banner
     *
     * @param BannerInterface $banner
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(BannerInterface $banner)
    {
        try {
            $this->resource->delete($banner);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Banner: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * Delete Banner by given Banner Identity
     *
     * @param string $bannerId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($bannerId)
    {
        return $this->delete($this->getById($bannerId));
    }
}
