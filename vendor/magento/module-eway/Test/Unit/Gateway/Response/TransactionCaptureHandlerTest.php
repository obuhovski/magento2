<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Eway\Test\Unit\Gateway\Response;

use Magento\Eway\Gateway\Response\TransactionCaptureHandler;

class TransactionCaptureHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TransactionCaptureHandler
     */
    private $handler;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $paymentDO;

    protected function setUp()
    {
        $this->paymentDO = $this->getMockBuilder('Magento\Payment\Gateway\Data\PaymentDataObjectInterface')
            ->getMockForAbstractClass();

        $this->handler = new TransactionCaptureHandler();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Payment data object should be provided
     */
    public function testHandleReadPaymentException()
    {
        $handlingSubject = [];
        $response = [];

        $this->handler->handle($handlingSubject, $response);
    }

    public function testHandle()
    {
        $handlingSubject = [
            'payment' => $this->paymentDO
        ];
        $response = [
            'TransactionID' => 12345678
        ];

        $orderPayment = $this->getMockBuilder('Magento\Sales\Model\Order\Payment')
            ->disableOriginalConstructor()
            ->getMock();

        $this->paymentDO->expects($this->once())
            ->method('getPayment')
            ->willReturn($orderPayment);
        $orderPayment->expects($this->once())
            ->method('setTransactionId')
            ->with($response['TransactionID']);
        $orderPayment->expects($this->once())
            ->method('setIsTransactionClosed')
            ->with(false);
        $orderPayment->expects($this->once())
            ->method('setShouldCloseParentTransaction')
            ->with(true);

        $this->handler->handle($handlingSubject, $response);
    }
}
