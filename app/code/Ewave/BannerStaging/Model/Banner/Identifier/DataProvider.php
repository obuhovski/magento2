<?php

namespace Ewave\BannerStaging\Model\Banner\Identifier;

use Magento\Banner\Model\ResourceModel\Banner\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Banner\Model\BannerFactory;
use \Magento\Ui\DataProvider\AbstractDataProvider as BannerDataProvider;

/**
 * Class DataProvider
 */
class DataProvider extends BannerDataProvider
{

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param BannerFactory $bannerFactory
     * @param array $meta
     * @param array $data
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
        $this->collection = $collectionFactory->create()->addStoresVisibility();
        $this->bannerFactory = $bannerFactory;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $banner) {
            $this->loadedData[$banner->getId()] = [
                'banner_id' => $banner->getId(),
                'title' => $banner->getName(),
            ];
        }

        return $this->loadedData;
    }
}
