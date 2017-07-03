<?php

namespace Ewave\BannerStaging\Model\Banner;

use Ewave\BannerStaging\Api\Data\BannerInterface;
use Ewave\BannerStaging\Model\Banner;
use Magento\Staging\Model\Entity\HydratorInterface;
use Magento\Backend\App\Action\Context;
use Magento\Staging\Model\Entity\RetrieverInterface;
use Magento\Framework\EntityManager\MetadataPool;

class Hydrator implements HydratorInterface
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var RetrieverInterface
     */
    protected $entityRetriever;

    /**
     * @param Context $context
     * @param RetrieverInterface $entityRetriever
     * @param MetadataPool $metadataPool
     */
    public function __construct(
        Context $context,
        RetrieverInterface $entityRetriever,
        MetadataPool $metadataPool
    ) {
        $this->context = $context;
        $this->entityRetriever = $entityRetriever;
        $this->metadataPool = $metadataPool;
    }

    /**
     * @inheritDoc
     */
    public function hydrate(array $data)
    {
        if (isset($data['is_enabled']) && $data['is_enabled'] === 'true') {
            $data['is_enabled'] = Banner::STATUS_ENABLED;
        }
        if (empty($data['banner_id'])) {
            $data['banner_id'] = null;
        }

        $model = null;
        if (isset($data['banner_id'])) {
            /** @var Banner $model */
            $model = $this->entityRetriever->getEntity($data['banner_id']);
            if ($model) {
                $entityMetadata = $this->metadataPool->getMetadata(BannerInterface::class);
                $linkField = $entityMetadata->getLinkField();
                $data[$linkField] = $model->getData($linkField);
                $data['created_in'] = $model->getCreatedIn();
                $data['updated_in'] = $model->getUpdatedIn();
            }
        }
        $model->setData($data);

        $this->context->getEventManager()->dispatch(
            'banner_prepare_save',
            ['banner' => $model, 'request' => $this->context->getRequest()]
        );

        return $model;
    }
}
