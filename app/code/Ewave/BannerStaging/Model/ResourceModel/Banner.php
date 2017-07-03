<?php

namespace Ewave\BannerStaging\Model\ResourceModel;

use Ewave\BannerStaging\Api\Data\BannerInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Banner
 */
class Banner extends \Magento\Banner\Model\ResourceModel\Banner
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var \Magento\Banner\Model\ResourceModel\Salesrule\CollectionFactory
     */
    private $salesruleColFactory;

    /**
     * @var \Magento\Banner\Model\ResourceModel\Catalogrule\CollectionFactory
     */
    private $catRuleColFactory;

    /**
     * @var \Magento\Banner\Model\Config
     */
    private $bannerConfig;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * Banner constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Banner\Model\Config $bannerConfig
     * @param \Magento\Banner\Model\ResourceModel\Salesrule\CollectionFactory $salesruleColFactory
     * @param \Magento\Banner\Model\ResourceModel\Catalogrule\CollectionFactory $catRuleColFactory
     * @param EntityManager $entityManager
     * @param MetadataPool $metadataPool
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Banner\Model\Config $bannerConfig,
        \Magento\Banner\Model\ResourceModel\Salesrule\CollectionFactory $salesruleColFactory,
        \Magento\Banner\Model\ResourceModel\Catalogrule\CollectionFactory $catRuleColFactory,
        EntityManager $entityManager,
        MetadataPool $metadataPool,
        $connectionName = null
    )
    {
        parent::__construct($context, $eventManager, $bannerConfig, $salesruleColFactory, $catRuleColFactory);
        $this->entityManager = $entityManager;
        $this->salesruleColFactory = $salesruleColFactory;
        $this->catRuleColFactory = $catRuleColFactory;
        $this->bannerConfig = $bannerConfig;
        $this->eventManager = $eventManager;
        $this->metadataPool = $metadataPool;
    }

    /**
     * @param AbstractModel $object
     * @return $this
     * @throws \Exception
     */
    public function save(AbstractModel $object)
    {
        if ($object->isDeleted()) {
            return $this->delete($object);
        }
        try {
            if ($object->isSaveAllowed()) {
//                $object->beforeSave();
//                $this->_beforeSave($object);
                $this->entityManager->save($object);
//                $this->processAfterSaves($object);
            }
        } catch (\Exception $e) {
            throw $e;
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(AbstractModel $object)
    {
        $this->entityManager->delete($object);
        return $this;
    }

    /**
     * Initialize banner resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_banner', 'banner_id');
        $this->_salesRuleTable = $this->getTable('magento_banner_salesrule');
        $this->_catalogRuleTable = $this->getTable('magento_banner_catalogrule');
        $this->_contentsTable = $this->getTable('magento_banner_content');
    }

    /**
     * Set filter by specified types
     *
     * @param string|array $types
     * @return $this
     */
    public function filterByTypes($types = [])
    {
        $this->_bannerTypesFilter = $this->bannerConfig->explodeTypes($types);
        return $this;
    }

    /**
     * @param int $rowId
     * @param array $contents
     * @param array $notuse
     * @return $this
     */
    public function saveStoreContents($rowId, $contents, $notuse = [])
    {
        $deleteByStores = [];
        if (!is_array($notuse)) {
            $notuse = [];
        }
        $connection = $this->getConnection();

        foreach ($contents as $storeId => $content) {
            if (!empty($content)) {
                $connection->insertOnDuplicate(
                    $this->_contentsTable,
                    ['row_id' => $rowId, 'store_id' => $storeId, 'banner_content' => $content],
                    ['banner_content']
                );
            } else {
                $deleteByStores[] = $storeId;
            }
        }
        if (!empty($deleteByStores) || !empty($notuse)) {
            $condition = [
                'row_id = ?' => $rowId,
                'store_id IN (?)' => array_merge($deleteByStores, array_keys($notuse)),
            ];
            $connection->delete($this->_contentsTable, $condition);
        }
        return $this;
    }

    /**
     * Delete unchecked catalog rules
     *
     * @param int $bannerId
     * @param array $rules
     * @return $this
     */
    public function saveCatalogRules($bannerId, $rules)
    {
        $connection = $this->getConnection();
        if (empty($rules)) {
            $rules = [0];
        } else {
            foreach ($rules as $ruleId) {
                $connection->insertOnDuplicate(
                    $this->_catalogRuleTable,
                    ['row_id' => $bannerId, 'rule_id' => $ruleId],
                    ['rule_id']
                );
            }
        }
        $condition = ['row_id=?' => $bannerId, 'rule_id NOT IN (?)' => $rules];
        $connection->delete($this->_catalogRuleTable, $condition);
        return $this;
    }

    /**
     * Delete unchecked sale rules
     *
     * @param int $bannerId
     * @param array $rules
     * @return $this
     */
    public function saveSalesRules($bannerId, $rules)
    {
        $connection = $this->getConnection();
        if (empty($rules)) {
            $rules = [0];
        } else {
            foreach ($rules as $ruleId) {
                $connection->insertOnDuplicate(
                    $this->_salesRuleTable,
                    ['row_id' => $bannerId, 'rule_id' => $ruleId],
                    ['rule_id']
                );
            }
        }
        $connection->delete($this->_salesRuleTable, ['row_id=?' => $bannerId, 'rule_id NOT IN (?)' => $rules]);
        return $this;
    }

    /**
     * Get all existing banner contents
     *
     * @param int $bannerId
     * @return array
     */
    public function getStoreContents($bannerId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->_contentsTable,
            ['store_id', 'banner_content']
        )->where(
            'row_id=?',
            $bannerId
        );
        return $connection->fetchPairs($select);
    }

    /**
     * Get banner content by specific store id
     *
     * @param int $bannerId
     * @param int $storeId
     * @return string
     */
    public function getStoreContent($bannerId, $storeId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            ['main_table' => $this->_contentsTable],
            'banner_content'
        )->where(
            'main_table.row_id = ?',
            $bannerId
        )->where(
            'main_table.store_id IN (?)',
            [$storeId, 0]
        )->order(
            'main_table.store_id DESC'
        );

        if ($this->_bannerTypesFilter) {
            $select->joinInner(
                ['banner' => $this->getTable('magento_banner')],
                'main_table.row_id = banner.row_id'
            );
            $filter = [];
            foreach ($this->_bannerTypesFilter as $type) {
                $filter[] = $connection->prepareSqlCondition('banner.types', ['finset' => $type]);
            }
            $select->where(implode(' OR ', $filter));
        }

        $this->eventManager->dispatch(
            'magento_banner_resource_banner_content_select_init',
            ['select' => $select]
        );

        return $connection->fetchOne($select);
    }

    /**
     * Get sales rule that associated to banner
     *
     * @param int $bannerId
     * @return array
     */
    public function getRelatedSalesRule($bannerId)
    {
        $connection = $this->getConnection();
        $select = $connection
            ->select()
            ->from($this->_salesRuleTable, [])
            ->where($this->_salesRuleTable.'.row_id = ?', $bannerId);
        if (!$this->_isSalesRuleJoined) {
            $select->join(
                ['rules' => $this->getTable('salesrule')],
                $this->_salesRuleTable . '.rule_id = rules.rule_id',
                ['rule_id']
            );
            $this->_isSalesRuleJoined = true;
        }
        $rules = $connection->fetchCol($select);
        return $rules;
    }

    /**
     * Get catalog rule that associated to banner
     *
     * @param int $bannerId
     * @return array
     */
    public function getRelatedCatalogRule($bannerId)
    {
        $connection = $this->getConnection();
        $select = $connection
            ->select()
            ->from($this->_catalogRuleTable, [])
            ->where($this->_catalogRuleTable.'.row_id = ?', $bannerId);
        if (!$this->_isCatalogRuleJoined) {
            $select->join(
                ['rules' => $this->getTable('catalogrule')],
                $this->_catalogRuleTable . '.rule_id = rules.rule_id',
                ['rule_id']
            );
            $this->_isCatalogRuleJoined = true;
        }

        $rules = $connection->fetchCol($select);
        return $rules;
    }

    /**
     * Get banners that associated to catalog rule
     *
     * @param int $ruleId
     * @return array
     */
    public function getRelatedBannersByCatalogRuleId($ruleId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->_catalogRuleTable,
            ['row_id']
        )->where(
            'rule_id = ?',
            $ruleId
        );
        return $connection->fetchCol($select);
    }

    /**
     * Get banners that associated to sales rule
     *
     * @param int $ruleId
     * @return array
     */
    public function getRelatedBannersBySalesRuleId($ruleId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from($this->_salesRuleTable, ['row_id'])->where('rule_id = ?', $ruleId);
        return $connection->fetchCol($select);
    }

    /**
     * Bind specified banners to catalog rule by rule id
     *
     * @param int $ruleId
     * @param array $banners
     * @return $this
     */
    public function bindBannersToCatalogRule($ruleId, $banners)
    {
        $connection = $this->getConnection();
        foreach ($banners as $bannerId) {
            $connection->insertOnDuplicate(
                $this->_catalogRuleTable,
                ['row_id' => $bannerId, 'rule_id' => $ruleId],
                ['rule_id']
            );
        }

        if (empty($banners)) {
            $banners = [0];
        }

        $connection->delete(
            $this->_catalogRuleTable,
            ['rule_id = ?' => $ruleId, 'row_id NOT IN (?)' => $banners]
        );
        return $this;
    }

    /**
     * Bind specified banners to sales rule by rule id
     *
     * @param int $ruleId
     * @param array $banners
     * @return $this
     */
    public function bindBannersToSalesRule($ruleId, $banners)
    {
        $connection = $this->getConnection();
        foreach ($banners as $bannerId) {
            $connection->insertOnDuplicate(
                $this->_salesRuleTable,
                ['row_id' => $bannerId, 'rule_id' => $ruleId],
                ['rule_id']
            );
        }

        if (empty($banners)) {
            $banners = [0];
        }

        $connection->delete($this->_salesRuleTable, ['rule_id = ?' => $ruleId, 'row_id NOT IN (?)' => $banners]);
        return $this;
    }

    /**
     * Get real existing active banner ids
     *
     * @return array
     */
    public function getActiveBannerIds()
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getMainTable(),
            ['row_id']
        )->where(
            'is_enabled  = ?',
            1
        );
        return $connection->fetchCol($select);
    }

    /**
     * Get banners content per store view
     *
     * @param array $bannerIds
     * @param int $storeId
     * @return array
     */
    public function getBannersContent(array $bannerIds, $storeId)
    {
        $result = [];
        foreach ($bannerIds as $bannerId) {
            $bannerContent = $this->getStoreContent($bannerId, $storeId);
            if (!empty($bannerContent)) {
                $result[$bannerId] = $bannerContent;
            }
        }
        return $result;
    }

    /**
     * Get banners IDs that related to sales rule and satisfy conditions
     *
     * @param array $appliedRules
     * @return array
     */
    public function getSalesRuleRelatedBannerIds(array $appliedRules)
    {
        return $this->salesruleColFactory->create()->addRuleIdsFilter($appliedRules)->getColumnValues('row_id');
    }

    /**
     * Get banners IDs that related to sales rule and satisfy conditions
     *
     * @param int $websiteId
     * @param int $customerGroupId
     * @return array
     */
    public function getCatalogRuleRelatedBannerIds($websiteId, $customerGroupId)
    {
        return $this->catRuleColFactory->create()->addWebsiteCustomerGroupFilter(
            $websiteId,
            $customerGroupId
        )->getColumnValues(
            'row_id'
        );
    }

    /**
     * @param AbstractModel $object
     * @param mixed $value
     * @param null $field
     * @return $this
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        $id = $this->getBannerId($object, $value, $field);
        if ($id) {
            $this->entityManager->load($object, $id);
        }
        return $this;
    }


    /**
     * @param AbstractModel $object
     * @param $value
     * @param null $field
     * @return bool
     */
    private function getBannerId(AbstractModel $object, $value, $field = null)
    {
        $entityMetadata = $this->metadataPool->getMetadata(BannerInterface::class);
        if (!$field) {
            $field = $entityMetadata->getIdentifierField();
        }
        $entityId = $value;
        if ($field != $entityMetadata->getIdentifierField() || $object->getStoreId()) {
            $select = $this->_getLoadSelect($field, $value, $object);
            $select->reset(Select::COLUMNS)
                ->columns($this->getMainTable() . '.' . $entityMetadata->getIdentifierField())
                ->limit(1);
            $result = $this->getConnection()->fetchCol($select);
            $entityId = count($result) ? $result[0] : false;
        }
        return $entityId;
    }

}
