<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VisualMerchandiser\Test\Unit\Controller\Adminhtml\Category;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Abstract shared functionality for controller tests
 */
class AbstractGrid extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $controllerClass;

    /**
     * @var \Magento\VisualMerchandiser\Controller\Adminhtml\Category\Grid
     */
    protected $gridController;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $rawFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Backend\App\Action\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Catalog\Model\Category
     */
    protected $category;

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $block;

    /**
     * Set up instances and mock objects
     */
    protected function setUp()
    {
        $this->context = $this->getMock('Magento\Backend\App\Action\Context', [], [], '', false);

        $this->category = $this->getMock('Magento\Catalog\Model\Category', ['setStoreId'], [], '', false);

        $this->objectManager = $this->getMockBuilder('Magento\Framework\ObjectManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->objectManager
            ->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->category));

        $this->request = $this->getMock('Magento\Framework\App\RequestInterface', [], [], '', false);

        $this->context
            ->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($this->request));

        $this->context
            ->expects($this->any())
            ->method('getObjectManager')
            ->will($this->returnValue($this->objectManager));

        $this->layoutFactory = $this->getMockBuilder('Magento\Framework\View\LayoutFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->block = $this->getMock('Magento\Framework\DataObject', ['toHtml', 'setPositionCacheKey'], [], '', false);
        $this->block
            ->expects($this->atLeastOnce())
            ->method('toHtml')
            ->will($this->returnValue('block-html'));

        $resultRaw = (new ObjectManager($this))->getObject('Magento\Framework\Controller\Result\Raw');
        $this->rawFactory = $this->getMock('Magento\Framework\Controller\Result\RawFactory', ['create'], [], '', false);
        $this->rawFactory
            ->expects($this->any())
            ->method('create')
            ->will($this->returnValue($resultRaw));

        $registry = $this->getMock('Magento\Framework\Registry', ['register'], [], '', false);
        $wysiwygConfig = $this->getMock('Magento\Cms\Model\Wysiwyg\Config', ['setStoreId'], [], '', false);
        $this->objectManager
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap([
                ['Magento\Framework\Registry', $registry],
                ['Magento\Cms\Model\Wysiwyg\Config', $wysiwygConfig]
            ]));

        $this->gridController = (new ObjectManager($this))->getObject($this->controllerClass, [
            'context' => $this->context,
            'resultRawFactory' => $this->rawFactory,
            'layoutFactory' => $this->layoutFactory
        ]);
    }

    protected function progressTest($block, $id)
    {
        $layout = $this->getMock('Magento\Framework\DataObject', ['createBlock'], [], '', false);
        $layout
            ->expects($this->any())
            ->method('createBlock')
            ->with(
                $this->equalTo($block),
                $this->equalTo($id)
            )
            ->will($this->returnValue($this->block));

        $this->layoutFactory
            ->expects($this->any())
            ->method('create')
            ->will($this->returnValue($layout));

        $this->assertInstanceOf(
            'Magento\Framework\Controller\Result\Raw',
            $this->gridController->execute()
        );
    }
}
