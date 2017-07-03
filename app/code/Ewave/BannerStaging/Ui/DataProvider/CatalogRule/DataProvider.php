<?php

namespace Ewave\BannerStaging\Ui\DataProvider\CatalogRule;

use Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Ewave\BannerStaging\Model\BannerFactory;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var BannerFactory
     */
    private $bannerFactory;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param BannerFactory $bannerFactory
     * @param array $meta
     * @param array $data
     * @internal param RuleFactory $salesRuleFactory
     * @internal param BannerFactory $bannerFactory
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        BannerFactory $bannerFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->request = $request;
        $this->collection = $collectionFactory->create();
        $this->bannerFactory = $bannerFactory;
    }

    /**
     * @return array
     */
    public function getData()
    {
        if ($id = (int) $this->request->getParam('id')) {
            $banner = $this->bannerFactory->create();
            $banner->load($id);
            return $this->getCollection()->addFieldToFilter('main_table.row_id', ['in' => $banner->getRelatedCatalogRule()])->toArray();
        }
        return parent::getData();
    }
}
