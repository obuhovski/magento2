<?php

namespace Ewave\BannerStaging\Model;

use Magento\Framework\Indexer\CacheContext;
use Magento\Staging\Model\StagingApplierInterface;

/**
 * Class BannerApplier
 */
class BannerApplier implements StagingApplierInterface
{
    /**
     * @var CacheContext
     */
    protected $cacheContext;

    /**
     * BannerApplier constructor
     * @param CacheContext $cacheContext
     */
    public function __construct(
        CacheContext $cacheContext
    ) {
        $this->cacheContext = $cacheContext;
    }

    /**
     * @param array $entityIds
     * @return void
     */
    public function execute(array $entityIds)
    {
        if ($entityIds) {
            $this->cacheContext->registerEntities('magento_banner', $entityIds);
        }
    }
}
