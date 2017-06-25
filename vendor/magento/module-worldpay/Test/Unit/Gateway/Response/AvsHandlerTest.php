<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Worldpay\Test\Unit\Gateway\Response;

use Magento\Worldpay\Gateway\Response\AvsHandler;

class AvsHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testHandle()
    {
        $response = [
            'AVS' => '1124'
        ];
        $additionalInfoExpectation = [
            ['postcode_avs', '1'],
            ['address_avs', '2'],
            ['country_comparison', '4']
        ];
        $fraudCases = '';

        $paymentDO = $this->getMockBuilder(
            'Magento\Payment\Gateway\Data\PaymentDataObjectInterface'
        )
            ->getMockForAbstractClass();
        $paymentInfo = $this->getMockBuilder(
            'Magento\Payment\Model\InfoInterface'
        )
            ->getMockForAbstractClass();
        $configMock = $this->getMockBuilder(
            'Magento\Payment\Gateway\ConfigInterface'
        )
            ->getMockForAbstractClass();

        $paymentDO->expects(static::any())
            ->method('getPayment')
            ->willReturn($paymentInfo);
        $paymentInfo->expects(static::exactly(3))
            ->method('setAdditionalInformation')
            ->willReturnMap($additionalInfoExpectation);
        $configMock->expects(static::once())
            ->method('getValue')
            ->with(AvsHandler::FRAUD_CASE)
            ->willReturn($fraudCases);

        $handler = new AvsHandler($configMock);
        $handler->handle(
            ['payment' => $paymentDO],
            $response
        );
    }

    public function testHandleFraud()
    {
        $response = [
            'AVS' => '1224'
        ];
        $additionalInfoExpectation = [
            ['postcode_avs', '2'],
            ['address_avs', '2'],
            ['country_comparison', '4']
        ];
        $fraudCases = '2,4';

        $paymentDO = $this->getMockBuilder(
            'Magento\Payment\Gateway\Data\PaymentDataObjectInterface'
        )
            ->getMockForAbstractClass();
        $paymentInfo = $this->getMockBuilder(
            'Magento\Sales\Model\Order\Payment'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $configMock = $this->getMockBuilder(
            'Magento\Payment\Gateway\ConfigInterface'
        )
            ->getMockForAbstractClass();

        $paymentDO->expects(static::any())
            ->method('getPayment')
            ->willReturn($paymentInfo);
        $paymentInfo->expects(static::exactly(3))
            ->method('setAdditionalInformation')
            ->willReturnMap($additionalInfoExpectation);
        $configMock->expects(static::once())
            ->method('getValue')
            ->with(AvsHandler::FRAUD_CASE)
            ->willReturn($fraudCases);
        $paymentInfo->expects(static::once())
            ->method('setIsFraudDetected')
            ->with(true);

        $handler = new AvsHandler($configMock);
        $handler->handle(
            ['payment' => $paymentDO],
            $response
        );
    }
}
