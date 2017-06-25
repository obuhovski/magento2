<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\BannerCustomerSegment\Test\Unit\Observer;

use Magento\BannerCustomerSegment\Observer\LoadCustomerSegmentRelations;

class LoadCustomerSegmentRelationsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Magento\BannerCustomerSegment\Observer\LoadCustomerSegmentRelations
     */
    private $loadCustomerSegmentRelationsObserver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_bannerSegmentLink;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_segmentHelper;

    protected function setUp()
    {
        $this->_bannerSegmentLink = $this->getMock(
            'Magento\BannerCustomerSegment\Model\ResourceModel\BannerSegmentLink',
            ['loadBannerSegments', 'saveBannerSegments', 'addBannerSegmentFilter', '__wakeup'],
            [],
            '',
            false
        );

        $this->_segmentHelper = $this->getMock(
            'Magento\CustomerSegment\Helper\Data',
            ['isEnabled', 'addSegmentFieldsToForm'],
            [],
            '',
            false
        );

        $this->loadCustomerSegmentRelationsObserver = new LoadCustomerSegmentRelations(
            $this->_segmentHelper,
            $this->_bannerSegmentLink
        );
    }

    protected function tearDown()
    {
        $this->_bannerSegmentLink = null;
        $this->loadCustomerSegmentRelationsObserver = null;
    }

    public function testLoadCustomerSegmentRelations()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(true));

        $banner = new \Magento\Framework\DataObject(['id' => 42]);
        $segmentIds = [123, 456];

        $this->_bannerSegmentLink->expects(
            $this->once()
        )->method(
            'loadBannerSegments'
        )->with(
            $banner->getId()
        )->will(
            $this->returnValue($segmentIds)
        );

        $this->loadCustomerSegmentRelationsObserver->execute(
            new \Magento\Framework\Event\Observer(
                [
                    'event' => new \Magento\Framework\DataObject(['banner' => $banner]),
                ]
            )
        );
        $this->assertEquals($segmentIds, $banner->getData('customer_segment_ids'));
    }

    public function testLoadCustomerSegmentRelationsDisabled()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(false));

        $banner = new \Magento\Framework\DataObject(['id' => 42]);

        $this->_bannerSegmentLink->expects($this->never())->method('loadBannerSegments');

        $this->loadCustomerSegmentRelationsObserver->execute(
            new \Magento\Framework\Event\Observer(
                [
                    'event' => new \Magento\Framework\DataObject(['banner' => $banner]),
                ]
            )
        );
    }
}
