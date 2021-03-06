<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TargetRule\Test\Unit\Observer;

use Magento\Catalog\Model\Product;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class CoreConfigSaveCommitAfterObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tested observer
     *
     * @var \Magento\TargetRule\Observer\CoreConfigSaveCommitAfterObserver
     */
    protected $_observer;

    /**
     * Product-Rule processor mock
     *
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_productRuleProcessorMock;

    protected function setUp()
    {
        $this->_productRuleProcessorMock = $this->getMock(
            '\Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Processor',
            [],
            [],
            '',
            false
        );

        $this->_observer = (new ObjectManager($this))->getObject(
            'Magento\TargetRule\Observer\CoreConfigSaveCommitAfterObserver',
            [
                'productRuleIndexerProcessor' => $this->_productRuleProcessorMock,
            ]
        );
    }

    public function testCoreConfigSaveCommitAfter()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', ['getDataObject'], [], '', false);
        $dataObject = $this->getMock('\Magento\Framework\DataObject', ['getPath', 'isValueChanged'], [], '', false);

        $dataObject->expects($this->once())
            ->method('getPath')
            ->will($this->returnValue('customer/magento_customersegment/is_enabled'));

        $dataObject->expects($this->once())
            ->method('isValueChanged')
            ->will($this->returnValue(true));

        $observerMock->expects($this->exactly(2))
            ->method('getDataObject')
            ->will($this->returnValue($dataObject));

        $this->_productRuleProcessorMock->expects($this->once())
            ->method('markIndexerAsInvalid');

        $this->_observer->execute($observerMock);
    }

    public function testCoreConfigSaveCommitAfterNoChanges()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', ['getDataObject'], [], '', false);
        $dataObject = $this->getMock('\Magento\Framework\DataObject', ['getPath', 'isValueChanged'], [], '', false);
        $dataObject->expects($this->once())
            ->method('getPath')
            ->will($this->returnValue('customer/magento_customersegment/is_enabled'));

        $dataObject->expects($this->once())
            ->method('isValueChanged')
            ->will($this->returnValue(false));

        $observerMock->expects($this->exactly(2))
            ->method('getDataObject')
            ->will($this->returnValue($dataObject));

        $this->_productRuleProcessorMock->expects($this->never())
            ->method('markIndexerAsInvalid');

        $this->_observer->execute($observerMock);
    }
}
