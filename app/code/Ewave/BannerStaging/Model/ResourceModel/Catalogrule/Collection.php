<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Collection of banner <-> catalog rule associations
 */
namespace Ewave\BannerStaging\Model\ResourceModel\Catalogrule;

class Collection extends \Magento\Banner\Model\ResourceModel\Catalogrule\Collection
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'magento_banner_catalogrule_collection';

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
        $this->_init('Magento\Framework\DataObject', 'Magento\CatalogRule\Model\ResourceModel\Rule');
        $this->setMainTable('magento_banner_catalogrule');
    }

    /**
     * Filter out disabled banners
     *
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
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
     * Add website id and customer group id filter to the collection
     *
     * @param int $websiteId
     * @param int $customerGroupId
     * @return \Magento\Banner\Model\ResourceModel\Catalogrule\Collection
     */
    public function addWebsiteCustomerGroupFilter($websiteId, $customerGroupId)
    {
        $this->getSelect()->join(
            ['rule_group_website' => $this->getTable('catalogrule_group_website')],
            'rule_group_website.rule_id = main_table.rule_id',
            []
        )->where(
            'rule_group_website.customer_group_id = ?',
            $customerGroupId
        )->where(
            'rule_group_website.website_id = ?',
            $websiteId
        );
        return $this;
    }
}
