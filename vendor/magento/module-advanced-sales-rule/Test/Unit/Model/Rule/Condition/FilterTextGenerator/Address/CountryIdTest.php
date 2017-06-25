<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdvancedSalesRule\Test\Unit\Model\Rule\Condition\FilterTextGenerator\Address;

use Magento\AdvancedSalesRule\Model\Rule\Condition\FilterTextGenerator\Address\CountryId;

class CountryIdTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test: generateFilterText()
     *
     * situation: if param is not an Address from a Quote, the generated filter text should be empty
     */
    public function testForEmptyGenerateFilterText()
    {
        $filterTextGenerator = new CountryId(['attribute' => 'kiwi']);
        $param = new \Magento\Framework\DataObject();
        $filterText = $filterTextGenerator->generateFilterText($param);
        $this->assertEmpty($filterText, "Expected 'filterText' to be empty");
    }

    /**
     * test: generateFilterText()
     *
     * situation: typical usage
     *
     * @dataProvider generateFilterTextDataProvider
     */
    public function testGenerateFilterText($attrCode, $attrValue, $expected)
    {
        $filterTextGenerator = new CountryId(['attribute' => $attrCode]);

        /** @var \Magento\Quote\Model\Quote\Address|\PHPUnit_Framework_MockObject_MockObject $quoteAddress */
        $quoteAddress = $this->buildQuoteAddress($attrCode, $attrValue);

        $filterText = $filterTextGenerator->generateFilterText($quoteAddress);
        $this->verifyResults($filterText, $expected);
    }

    /**
     * @return array
     */
    public function generateFilterTextDataProvider()
    {
        return [
            'typical' => [
                'attrCode' => 'kiwi',
                'attrValue' => 'fearless',
                'expected' => ':kiwi:fearless',
            ],

            'null value' => [
                'attrCode' => 'kiwi',
                'attrValue' => null,
                'expected' => null,
            ],

            'array value' => [
                'attrCode' => 'kiwi',
                'attrValue' => ['some', 'random', 'array'],
                'expected' => null,
            ],

            'object value' => [
                'attrCode' => 'kiwi',
                'attrValue' => new \Magento\Framework\DataObject(),
                'expected' => null,
            ],
        ];
    }

    // --- helpers ------------------------------------------------------------

    protected function verifyResults(array $filterText, $expectedValue)
    {
        if ($expectedValue === null) {
            $this->assertEmpty($filterText, "'filterText' should be empty. Actual is" . print_r($filterText, true));
            return;
        }

        if (!$this->findMe($expectedValue, $filterText)) {
            $this->fail("'filterText' does not contain expected value: " . $expectedValue);
        }

        $this->assertTrue(sizeof($filterText) == 1, "Expected 'filterText' to only have 1 entry in it");
    }

    protected function findMe($needle, array $haystack)
    {
        foreach ($haystack as $entry) {
            if (strpos($entry, $needle) !== false) {
                return true;
            }
        }
        return false;
    }

    protected function buildQuoteAddress($attrCode, $attrValue)
    {
        $className = '\Magento\Quote\Model\Quote\Address';
        $quoteAddress = $this->getMock($className, ['getData'], [], '', false);
        $quoteAddress->expects($this->once())->method('getData')->with($attrCode)->will($this->returnValue($attrValue));

        return $quoteAddress;
    }
}
