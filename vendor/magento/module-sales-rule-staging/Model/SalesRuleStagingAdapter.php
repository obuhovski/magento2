<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\SalesRuleStaging\Model;

use Magento\SalesRule\Model\Converter\ToDataModel;

/**
 * Class SalesRuleStagingAdapter
 * @deprecated
 */
class SalesRuleStagingAdapter
{
    /**
     * @var SalesRuleStaging
     */
    private $salesRuleStaging;

    /**
     * @var ToDataModel
     */
    private $toDataModel;

    /**
     * SalesRuleStagingAdapter constructor.
     *
     * @param SalesRuleStaging $salesRuleStaging
     * @param ToDataModel $toDataMode
     */
    public function __construct(
        SalesRuleStaging $salesRuleStaging,
        ToDataModel $toDataModel
    ) {
        $this->salesRuleStaging = $salesRuleStaging;
        $this->toDataModel = $toDataModel;
    }


    /**
     * @param \Magento\SalesRule\Model\Rule $salesRule
     * @param string $version
     * @param array $arguments
     * @return bool
     */
    public function schedule(\Magento\SalesRule\Model\Rule $salesRule, $version, $arguments = [])
    {
        $dataObject = $this->toDataModel->toDataModel($salesRule);
        return (bool)$this->salesRuleStaging->schedule($dataObject, $version, $arguments);
    }

    /**
     * @param \Magento\SalesRule\Model\Rule $salesRule
     * @param string $version
     * @return bool
     */
    public function unschedule(\Magento\SalesRule\Model\Rule $salesRule, $version)
    {
        $dataObject = $this->toDataModel->toDataModel($salesRule);
        return $this->salesRuleStaging->unschedule($dataObject, $version);
    }
}
