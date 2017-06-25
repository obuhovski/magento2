<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogStaging\Test\Unit\Ui\Component\Listing\Column\Product;

use Magento\CatalogStaging\Ui\Component\Listing\Column\Product\UrlProvider;

class UrlProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $urlBuilderMock;

    /**
     * @var UrlProvider
     */
    private $urlProvider;

    protected function setUp()
    {
        $this->urlBuilderMock = $this->getMock('Magento\Framework\Url', [], [], '', false);
        $this->urlProvider = new UrlProvider(
            $this->urlBuilderMock
        );
    }

    public function testGetUrl()
    {
        $item = [
            'entity_id' => 1,
        ];
        $this->urlBuilderMock->expects($this->once())->method('getUrl')->with(
            'catalog/product/view',
            [
                'id' => $item['entity_id'],
                '_nosid' => true,

            ]
        );

        $this->urlProvider->getUrl($item);
    }
}
