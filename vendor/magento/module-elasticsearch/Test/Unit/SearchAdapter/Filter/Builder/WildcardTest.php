<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Elasticsearch\Test\Unit\SearchAdapter\Filter\Builder;

use Magento\Elasticsearch\SearchAdapter\Filter\Builder\Wildcard;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

/**
 * @see \Magento\Elasticsearch\SearchAdapter\Filter\Builder\Wildcard
 */
class WildcardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Wildcard
     */
    private $model;

    /**
     * @var \Magento\Elasticsearch\Model\Adapter\FieldMapperInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fieldMapper;

    /**
     * @var \Magento\Framework\Search\Request\Filter\Wildcard|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filterInterface;

    /**
     * Set up test environment.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->fieldMapper = $this->getMockBuilder('Magento\Elasticsearch\Model\Adapter\FieldMapperInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->filterInterface = $this->getMockBuilder('Magento\Framework\Search\Request\Filter\Wildcard')
            ->disableOriginalConstructor()
            ->setMethods([
                'getField',
                'getValue',
            ])
            ->getMock();

        $objectManagerHelper = new ObjectManagerHelper($this);
        $this->model = $objectManagerHelper->getObject(
            '\Magento\Elasticsearch\SearchAdapter\Filter\Builder\Wildcard',
            [
                'fieldMapper' => $this->fieldMapper
            ]
        );
    }

    public function testBuildFilter()
    {
        $this->fieldMapper->expects($this->any())
            ->method('getFieldName')
            ->willReturn('field');

        $this->filterInterface->expects($this->any())
            ->method('getField')
            ->willReturn('field');

        $this->model->buildFilter($this->filterInterface);
    }
}
