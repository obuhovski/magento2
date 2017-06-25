<?php
namespace Ewave\BannerStaging\Model\Banner;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Staging\Model\Entity\DataProvider\MetadataProvider;
use Magento\Banner\Model\ResourceModel\Banner\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Banner\Model\BannerFactory;

/**
 * Class DataProvider
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $pageCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param MetadataProvider $metadataProvider
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $pageCollectionFactory,
        DataPersistorInterface $dataPersistor,
        MetadataProvider $metadataProvider,
        array $meta = [],
        array $data = []
    ) {
        $meta = array_replace_recursive($meta, $metadataProvider->getMetadata());
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $pageCollectionFactory,
            $dataPersistor,
            $meta,
            $data
        );
    }
}
