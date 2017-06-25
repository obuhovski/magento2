<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CmsStaging\Test\Unit\Ui\Component\DataProvider;

use Magento\CmsStaging\Ui\Component\DataProvider\UpdatePlugin;

class UpdatePluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UpdatePlugin
     */
    private $plugin;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var \Magento\Staging\Api\UpdateRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $updateRepositoryMock;

    /**
     * @var \Magento\Framework\Api\FilterBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterBuilderMock;

    protected function setUp()
    {
        $this->requestMock = $this->getMock('Magento\Framework\App\RequestInterface');
        $this->updateRepositoryMock = $this->getMock('Magento\Staging\Api\UpdateRepositoryInterface');
        $this->filterBuilderMock = $this->getMockBuilder('Magento\Framework\Api\FilterBuilder')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->plugin = new UpdatePlugin(
            $this->requestMock,
            $this->updateRepositoryMock,
            $this->filterBuilderMock
        );
    }

    /**
     * @param int|null $updateId
     * @param int $requestUpdateId
     * @param bool $isUpdateExists
     * @dataProvider getUpdateDataProvider
     */
    public function testBeforeGetSearchResult($updateId, $requestUpdateId, $isUpdateExists)
    {
        /** @var \Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface $dataProviderMock */
        $dataProviderMock = $this->getMockBuilder(
            'Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface'
        )->getMockForAbstractClass();

        $filterMock = $this->getMockBuilder('Magento\Framework\Api\Filter')
            ->disableOriginalConstructor()
            ->getMock();
        $this->filterBuilderMock->expects($this->any())
            ->method('create')
            ->willReturn($filterMock);
        $updateMock = $this->getMock('Magento\Staging\Model\Update', [], [], '', false);
        $updateMock->expects($this->any())->method('getId')->willReturn($isUpdateExists ? $updateId : false);

        $this->requestMock->expects($this->any())->method('getParam')->willReturn($requestUpdateId);
        $this->updateRepositoryMock->expects($this->any())->method('get')->with($updateId)->willReturn($updateMock);

        if ($isUpdateExists) {
            $dataProviderMock->expects($this->once())->method('addFilter')->with($filterMock);
        } else {
            $dataProviderMock->expects($this->never())->method('addFilter');
        }

        $this->plugin->beforeGetSearchResult($dataProviderMock);
    }

    /**
     * Update data provider
     *
     * @return array
     */
    public function getUpdateDataProvider()
    {
        return [
            [1, 1, true],//update exists
            [123, 123, false],//update does not exist
        ];
    }
}
