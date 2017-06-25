<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ScalableCheckout\Test\Unit\Model\ResourceModel\Quote\Item;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class ConsumerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ScalableCheckout\Model\ResourceModel\Quote\Item\Consumer
     */
    private $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $itemMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $cartRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $loggerMock;

    protected function setUp()
    {
        $this->itemMock = $this->getMock(\Magento\Quote\Model\ResourceModel\Quote\Item::class, [], [], '', false);
        $this->loggerMock = $this->getMock(\Psr\Log\LoggerInterface::class, [], [], '', false);
        $this->cartRepositoryMock = $this->getMockForAbstractClass(
            \Magento\Quote\Api\CartRepositoryInterface::class,
            [],
            '',
            false,
            false
        );

        $this->model = new \Magento\ScalableCheckout\Model\ResourceModel\Quote\Item\Consumer(
            $this->itemMock,
            $this->cartRepositoryMock,
            $this->loggerMock
        );
    }

    public function testProcessMessage()
    {
        $mainTable = 'quote_item';
        $productId = 42;
        $quoteIds = [24];
        $connectionMock = $this->getMockForAbstractClass(AdapterInterface::class, [], '', false, false);
        $productMock = $this->getMockForAbstractClass(ProductInterface::class, [], '', false, false);
        $quoteMock = $this->getMockForAbstractClass(\Magento\Quote\Api\Data\CartInterface::class, [], '', false, false);
        $selectMock = $this->getMock(\Magento\Framework\DB\Select::class, [], [], '', false);
        $this->itemMock->expects($this->once())->method('getConnection')->willReturn($connectionMock);
        $connectionMock->expects($this->once())->method('select')->willReturn($selectMock);
        $selectMock->expects($this->once())->method('reset')->willReturnSelf();
        $this->itemMock->expects($this->atLeastOnce())->method('getMainTable')->willReturn($mainTable);
        $selectMock->expects($this->once())->method('from')->with($mainTable, ['quote_id']);
        $productMock->expects($this->atLeastOnce())->method('getId')->willReturn($productId);
        $selectMock->expects($this->once())->method('where')->with('product_id = ?', $productId);
        $connectionMock->expects($this->once())->method('fetchCol')->with($selectMock)->willReturn($quoteIds);
        $connectionMock->expects($this->once())->method('delete')->with($mainTable, 'product_id = ' . $productId);
        $this->cartRepositoryMock->expects($this->once())->method('get')->with($quoteIds[0])->willReturn($quoteMock);
        $this->cartRepositoryMock->expects($this->once())->method('save')->with($quoteMock);

        $this->loggerMock->expects($this->never())->method('critical');

        $this->model->processMessage($productMock);
    }

    public function testProcessMessageWithException()
    {
        $exception = new \Exception(__("Fatal Error"));
        $mainTable = 'quote_item';
        $productId = 42;
        $quoteIds = [24];
        $connectionMock = $this->getMockForAbstractClass(AdapterInterface::class, [], '', false, false);
        $productMock = $this->getMockForAbstractClass(ProductInterface::class, [], '', false, false);
        $quoteMock = $this->getMockForAbstractClass(\Magento\Quote\Api\Data\CartInterface::class, [], '', false, false);
        $selectMock = $this->getMock(\Magento\Framework\DB\Select::class, [], [], '', false);
        $this->itemMock->expects($this->once())->method('getConnection')->willReturn($connectionMock);
        $connectionMock->expects($this->once())->method('select')->willReturn($selectMock);
        $selectMock->expects($this->once())->method('reset')->willReturnSelf();
        $this->itemMock->expects($this->atLeastOnce())->method('getMainTable')->willReturn($mainTable);
        $selectMock->expects($this->once())->method('from')->with($mainTable, ['quote_id']);
        $productMock->expects($this->atLeastOnce())->method('getId')->willReturn($productId);
        $selectMock->expects($this->once())->method('where')->with('product_id = ?', $productId);
        $connectionMock->expects($this->once())->method('fetchCol')->with($selectMock)->willReturn($quoteIds);
        $connectionMock->expects($this->once())->method('delete')->with($mainTable, 'product_id = ' . $productId);

        $this->cartRepositoryMock->expects($this->once())->method('get')->with($quoteIds[0])
            ->willThrowException($exception);
        $this->cartRepositoryMock->expects($this->never())->method('save')->with($quoteMock);
        $this->loggerMock->expects($this->once())->method('critical')->with($exception);

        $this->model->processMessage($productMock);
    }
}
