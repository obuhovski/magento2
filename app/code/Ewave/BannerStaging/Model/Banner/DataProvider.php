<?php
namespace Ewave\BannerStaging\Model\Banner;

use Ewave\BannerStaging\Model\Banner;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Staging\Model\Entity\DataProvider\MetadataProvider;
use Magento\Banner\Model\ResourceModel\Banner\CollectionFactory;

/**
 * Class DataProvider
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $bannerCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param MetadataProvider $metadataProvider
     * @param RequestInterface $request
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $bannerCollectionFactory,
        DataPersistorInterface $dataPersistor,
        MetadataProvider $metadataProvider,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        $requestFieldName = $request->getParam('id') ? 'id' : 'banner_id';
        $meta = array_replace_recursive($meta, $metadataProvider->getMetadata());
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );
        $this->dataPersistor = $dataPersistor;
        $this->collection = $bannerCollectionFactory->create();
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

        /** @var Banner $banner */
        foreach ($items as $banner) {
            $banner->afterLoad();
            $this->loadedData[$banner->getId()] = $banner->getData();
        }

        $data = $this->dataPersistor->get('magento_banner');
        if (!empty($data)) {
            $banner = $this->collection->getNewEmptyItem();
            $banner->setData($data);
            $this->loadedData[$banner->getId()] = $banner->getData();
            $this->dataPersistor->clear('magento_banner');
        }

        return $this->loadedData;
    }
}
