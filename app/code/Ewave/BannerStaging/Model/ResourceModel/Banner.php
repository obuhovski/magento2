<?php

namespace Ewave\BannerStaging\Model\ResourceModel;

use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Model\AbstractModel;

/**
 * Class PageApplier
 */
class Banner extends \Magento\Banner\Model\ResourceModel\Banner
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Banner constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Banner\Model\Config $bannerConfig
     * @param \Magento\Banner\Model\ResourceModel\Salesrule\CollectionFactory $salesruleColFactory
     * @param \Magento\Banner\Model\ResourceModel\Catalogrule\CollectionFactory $catRuleColFactory
     * @param EntityManager $entityManager
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Banner\Model\Config $bannerConfig,
        \Magento\Banner\Model\ResourceModel\Salesrule\CollectionFactory $salesruleColFactory,
        \Magento\Banner\Model\ResourceModel\Catalogrule\CollectionFactory $catRuleColFactory,
        EntityManager $entityManager,
        $connectionName = null
    ) {
        parent::__construct($context, $eventManager, $bannerConfig, $salesruleColFactory, $catRuleColFactory);
        $this->entityManager = $entityManager;
    }

    /**
     * @param AbstractModel $object
     * @return $this
     * @throws \Exception
     */
    public function save(AbstractModel $object)
    {
        $this->entityManager->save($object);
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
}
