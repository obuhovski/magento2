<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdvancedCheckout\Test\Unit\Plugin\Checkout\CustomerData;

use Magento\AdvancedCheckout\Plugin\Checkout\CustomerData\Cart as CartPlugin;
use Magento\AdvancedCheckout\Model\Cart;

class CartTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CartPlugin
     */
    private $cartPlugin;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Cart
     */
    private $advancedCartMock;

    protected function setUp()
    {
        $this->advancedCartMock = $this->getMock('\Magento\AdvancedCheckout\Model\Cart', [], [], '', false);
        $this->cartPlugin = new CartPlugin(
            $this->advancedCartMock
        );
    }

    /**
     * @param array $failedItems
     * @param string $expectedMessage
     * @dataProvider dataProvider
     */
    public function testAfterGetSectionDataSetsMessageIfCartHasItemsThatRequireAttention($failedItems, $expectedMessage)
    {
        $cartMock = $this->getMock('Magento\Checkout\CustomerData\Cart', [], [], '', false);
        $this->advancedCartMock->expects($this->any())->method('getFailedItems')->willReturn($failedItems);
        $result = $this->cartPlugin->afterGetSectionData($cartMock, []);
        $this->assertContains($expectedMessage, $result);
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            [[], ''],
            [['product_sku'], '1 item(s) need your attention.'],
        ];
    }
}
