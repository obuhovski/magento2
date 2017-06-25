<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\MessageQueue\Test\Unit;

use Magento\Framework\MessageQueue\ConfigInterface as QueueConfig;
use Magento\Framework\MessageQueue\ConsumerConfiguration;
use Magento\Framework\MessageQueue\ConsumerFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class ConsumerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var QueueConfig|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queueConfigMock;

    const TEST_CONSUMER_NAME = "test_consumer_name";
    const TEST_CONSUMER_CONNECTION = "test_consumer_connection";
    const TEST_CONSUMER_QUEUE = "test_consumer_queue";
    const TEST_CONSUMER_METHOD = "test_consumer_method";

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->queueConfigMock = $this->getMockBuilder('Magento\Framework\MessageQueue\ConfigInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage pecified consumer "test_consumer_name" is not declared.
     */
    public function testUndeclaredConsumerName()
    {
        $consumerFactory = $this->objectManager->getObject(
            'Magento\Framework\MessageQueue\ConsumerFactory',
            [
                'queueConfig' => $this->queueConfigMock,
            ]
        );
        $consumerFactory->get(self::TEST_CONSUMER_NAME);
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Could not find an implementation type for connection "test_consumer_connection".
     */
    public function testConsumerNotInjectedIntoClass()
    {
        $consumers = [
            [
                'type' => ['nonExistentType' => ''],
                'connectionName' => self::TEST_CONSUMER_CONNECTION,
            ]
        ];
        $consumerFactory = $this->getConsumerFactoryInstance($consumers);
        $consumerFactory->get(self::TEST_CONSUMER_NAME);
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Could not find an implementation type for connection "test_consumer_connection".
     */
    public function testNoConnectionInjectedForConsumer()
    {
        $consumerType = 'async';
        $consumerTypeValue = 'Magento\Framework\MessageQueue\Model\TestConsumer';
        $consumers = [
            [
                'type' => [$consumerType => $consumerTypeValue],
                'connectionName' => 'randomPublisherConnection',
            ]
        ];
        $consumerFactory = $this->getConsumerFactoryInstance($consumers);
        $consumerFactory->get(self::TEST_CONSUMER_NAME);
    }

    public function testConnectionInjectedForConsumer()
    {
        $consumerType = 'async';
        $consumerTypeValue = 'Magento\Framework\MessageQueue\Model\TestConsumer';
        $consumers = [
            [
                'type' => [$consumerType => $consumerTypeValue],
                'connectionName' => self::TEST_CONSUMER_CONNECTION,
            ]
        ];
        $consumerFactory = $this->getConsumerFactoryInstance($consumers);
        $consumerInstanceMock = $this->getMockBuilder($consumerTypeValue)->getMock();
        $this->assertInstanceOf(get_class($consumerInstanceMock), $consumerFactory->get(self::TEST_CONSUMER_NAME));
    }

    /**
     * Return Consumer Factory with mocked objects
     *
     * @param array $consumers
     * @return \Magento\Framework\MessageQueue\ConsumerFactory
     */
    private function getConsumerFactoryInstance($consumers)
    {
        $consumerTypeValue = 'Magento\Framework\MessageQueue\Model\TestConsumer';
        $handlerTypeValue = 'Magento\Framework\DataObject';
        $consumerType = 'async';

        $this->queueConfigMock->expects($this->any())
            ->method('getConsumer')
            ->will(
                $this->returnValue(
                    [
                        QueueConfig::CONSUMER_CONNECTION => self::TEST_CONSUMER_CONNECTION,
                        QueueConfig::CONSUMER_NAME => self::TEST_CONSUMER_NAME,
                        QueueConfig::CONSUMER_QUEUE => self::TEST_CONSUMER_QUEUE,
                        QueueConfig::CONSUMER_TYPE => QueueConfig::CONSUMER_TYPE_ASYNC,
                        QueueConfig::CONSUMER_HANDLERS => [
                            'topicName' => [
                                "defaultHandler" => [
                                    "type" => $handlerTypeValue,
                                    "method" => self::TEST_CONSUMER_METHOD
                                ]
                            ]
                        ]

                    ]
                )
            );

        $consumerInstanceMock = $this->getMockBuilder($consumerTypeValue)->getMock();
        $consumerMock = $this->getMockBuilder('Magento\Framework\MessageQueue\ConsumerInterface')
            ->setMethods(['configure'])
            ->getMockForAbstractClass();

        $consumerConfigurationMock =
            $this->getMockBuilder('Magento\Framework\MessageQueue\ConsumerConfigurationInterface')
                ->disableOriginalConstructor()
                ->getMockForAbstractClass();
        $consumerConfigurationMock->expects($this->any())->method('getType')->willReturn($consumerType);

        $objectManagerMock = $this->getMockBuilder('Magento\Framework\ObjectManagerInterface')
            ->setMethods(['create'])
            ->getMockForAbstractClass();

        $objectManagerMock->expects($this->any())
            ->method('create')
            ->willReturnOnConsecutiveCalls($consumerMock, $consumerConfigurationMock, $consumerInstanceMock);

        return $this->objectManager->getObject(
            'Magento\Framework\MessageQueue\ConsumerFactory',
            [
                'queueConfig' => $this->queueConfigMock,
                'objectManager' => $objectManagerMock,
                'consumers' => $consumers
            ]
        );
    }
}
