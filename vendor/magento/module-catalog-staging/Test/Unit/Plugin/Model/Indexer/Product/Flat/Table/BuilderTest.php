<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogStaging\Test\Unit\Plugin\Model\Indexer\Product\Flat\Table;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Indexer\Product\Flat\Table\BuilderInterface;
use Magento\Framework\EntityManager\EntityMetadataInterface;

/**
 * Class BuilderTest
 */
class BuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\EntityManager\MetadataPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $metadataPoolMock;

    /**
     * @var EntityMetadataInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $metadataMock;

    /**
     * @var BuilderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $builderMock;

    /**
     * @var \Magento\CatalogStaging\Plugin\Model\Indexer\Product\Flat\Table\Builder
     */
    private $plugin;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->metadataPoolMock = $this->getMockBuilder(\Magento\Framework\EntityManager\MetadataPool::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->metadataMock = $this->getMockBuilder(EntityMetadataInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->builderMock = $this->getMockBuilder(BuilderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->plugin = $objectManagerHelper->getObject(
            \Magento\CatalogStaging\Plugin\Model\Indexer\Product\Flat\Table\Builder::class,
            [
                'metadataPool' => $this->metadataPoolMock
            ]
        );
    }

    public function testAfterGetTable()
    {
        $linkField = 'row_id';
        $this->metadataPoolMock->expects($this->once())
            ->method('getMetadata')
            ->with(ProductInterface::class)
            ->willReturn($this->metadataMock);
        $this->metadataMock->expects($this->once())->method('getLinkField')
            ->willReturn($linkField);
        $result = $this->getMockBuilder(\Magento\Framework\DB\Ddl\Table::class)
            ->disableOriginalConstructor()
            ->getMock();
        $result->expects($this->once())->method('addColumn')->with(
            $linkField,
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER
        )->willReturnSelf();
        $this->assertEquals($result, $this->plugin->afterGetTable($this->builderMock, $result));
    }
}

