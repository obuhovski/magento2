<?php

namespace Ewave\BannerStaging\Ui\DataProvider\SalesRule;

use Ewave\BannerStaging\Api\BannerRepositoryInterface;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Ewave\BannerStaging\Model\BannerFactory;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var BannerRepositoryInterface
     */
    protected $bannerRepository;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param BannerRepositoryInterface $bannerRepository
     * @param array $meta
     * @param array $data
     * @internal param BannerFactory $bannerFactory
     * @internal param RuleFactory $salesRuleFactory
     * @internal param BannerFactory $bannerFactory
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        BannerRepositoryInterface $bannerRepository,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->request = $request;
        $this->collection = $collectionFactory->create();
        $this->bannerRepository = $bannerRepository;
    }

    /**
     * @return array
     */
    public function getData()
    {
        $selectedRules = [];
        if ($id = (int) $this->request->getParam('banner_id')) {
            $banner = $this->bannerRepository->getById($id);
            $selectedRules = $this->getCollection()
                ->addFieldToFilter('main_table.row_id', ['in' => $banner->getRelatedSalesRule()])
                ->toArray();
        }
        return $selectedRules;
    }
}
