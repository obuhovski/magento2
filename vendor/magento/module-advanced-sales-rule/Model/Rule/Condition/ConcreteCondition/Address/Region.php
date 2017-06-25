<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdvancedSalesRule\Model\Rule\Condition\ConcreteCondition\Address;

use Magento\AdvancedSalesRule\Model\Rule\Condition\ConcreteCondition\AbstractFilterableCondition;

class Region extends AbstractFilterableCondition
{
    const FILTER_TEXT_GENERATOR_CLASS =
        'Magento\AdvancedSalesRule\Model\Rule\Condition\FilterTextGenerator\Address\Region';

    /**
     * @return string
     */
    protected function getFilterTextPrefix()
    {
        return self::FILTER_TEXT_PREFIX;
    }

    /**
     * @return string
     */
    protected function getFilterTextGeneratorClass()
    {
        return self::FILTER_TEXT_GENERATOR_CLASS;
    }
}
