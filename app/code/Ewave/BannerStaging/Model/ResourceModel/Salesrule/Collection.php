<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Collection of banner <-> sales rule associations
 */
namespace Ewave\BannerStaging\Model\ResourceModel\Salesrule;

class Collection extends \Magento\Banner\Model\ResourceModel\Salesrule\Collection
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'magento_banner_salesrule_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'collection';

    /**
     * Define collection item type and corresponding table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Framework\DataObject', 'Magento\SalesRule\Model\ResourceModel\Rule');
        $this->setMainTable('magento_banner_salesrule');
    }

    /**
     * Filter out disabled banners
     *
     * @return $this2
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(['main_table' => $this->getMainTable()]);
        $this->getSelect()->join(
            ['banner' => $this->getTable('magento_banner')],
            'banner.row_id = main_table.row_id AND banner.is_enabled = 1',
            []
        )->group(
            'main_table.row_id'
        );
        return $this;
    }

    /**
     * Add sales rule ids filter to the collection
     *
     * @param array $ruleIds
     * @return $this
     */
    public function addRuleIdsFilter(array $ruleIds)
    {
        if (!$ruleIds) {
            // force to match no rules
            $ruleIds = [0];
        }
        $this->addFieldToFilter('main_table.rule_id', ['in' => $ruleIds]);
        return $this;
    }
}
