<?php

namespace Ewave\EventGenerateReport\Model\ResourceModel\Club\Event;


use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;

class OrderItem extends \Magento\Sales\Model\ResourceModel\Order\Item\Collection
{
    /**
     * Order status
     *
     * @var string
     */
    protected $_orderStatus = null;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        Snapshot $entitySnapshot,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null)
    {
        parent::__construct(
            $entityFactory, $logger, $fetchStrategy, $eventManager, $entitySnapshot, $connection, $resource
        );
    }

    /**
     * Apply filters common to reports
     *
     * @return $this
     */
    protected function _beforeLoad()
    {
        parent::_beforeLoad();

//        $this->_applyAggregatedTable();
//        $this->_applyDateRangeFilter();
//        $this->_applyStoresFilter();
//        $this->_applyCustomFilter();
        return $this;
    }
}