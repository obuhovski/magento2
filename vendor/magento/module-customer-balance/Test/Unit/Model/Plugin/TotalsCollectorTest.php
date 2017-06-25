<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerBalance\Test\Unit\Model\Plugin;

use Magento\CustomerBalance\Model\Plugin\TotalsCollector;

class TotalsCollectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reward\Model\Plugin\TotalsCollector
     */
    private $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $quoteMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $totalsCollectorMock;

    protected function setUp()
    {
        $this->totalsCollectorMock = $this->getMock('Magento\Quote\Model\Quote\TotalsCollector', [], [], '', false);
        $this->quoteMock = $this->getMock(
            'Magento\Quote\Model\Quote',
            [
                'setBaseCustomerBalAmountUsed',
                'setCustomerBalanceAmountUsed',
            ],
            [],
            '',
            false
        );
        $this->model = new TotalsCollector();
    }

    public function testBeforeCollectResetsRewardAmount()
    {
        $this->quoteMock->expects($this->once())->method('setBaseCustomerBalAmountUsed')->with(0);
        $this->quoteMock->expects($this->once())->method('setCustomerBalanceAmountUsed')->with(0);
        $this->model->beforeCollect($this->totalsCollectorMock, $this->quoteMock);
    }
}
