<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogStaging\Plugin\Model\Indexer\Product\Flat\Table;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * Class Builder
 */
class Builder
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * Indexer constructor.
     *
     * @param MetadataPool $metadataPool
     */
    public function __construct(MetadataPool $metadataPool)
    {
        $this->metadataPool = $metadataPool;
    }

    /**
     * @param \Magento\Catalog\Model\Indexer\Product\Flat\Table\BuilderInterface $subject
     * @param \Magento\Framework\DB\Ddl\Table $result
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @return \Magento\Framework\DB\Ddl\Table
     */
    public function afterGetTable(
        \Magento\Catalog\Model\Indexer\Product\Flat\Table\BuilderInterface $subject,
        \Magento\Framework\DB\Ddl\Table $result
    ) {
        $linkField = $this->metadataPool->getMetadata(ProductInterface::class)->getLinkField();
        $result->addColumn($linkField, \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER);

        return $result;
    }
}
