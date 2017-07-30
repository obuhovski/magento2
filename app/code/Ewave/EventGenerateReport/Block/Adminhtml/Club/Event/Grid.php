<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ewave\EventGenerateReport\Block\Adminhtml\Club\Event;

use Magento\Framework\App\ResourceConnection\ConnectionAdapterInterface;

/**
 * Class Grid
 * @package Ewave\EventGenerateReport\Block\Adminhtml\Club\Event
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Ewave\EventGenerateReport\Model\ResourceModel\Club\Event\OrderItemFactory
     */
    private $orderItemCollectionFactory;

    /**
     * @var ConnectionAdapterInterface
     */
    private $connection;

    protected $fields = [
        'event_name',
        'event_date',
        'gl_code',
        'title',
        'first_name',
        'last_name',
        'booking_name',
        'company_name',
        'address1',
        'address2',
        'city',
        'state',
        'post_code',
        'country',
        'telephone',
        'email',
        'order_date',
        'order_edited_date',
        'order_status',
        'order_item',
        'qty',
        'product_price',
        'order_price',
        'order_total',
        'amount_paid',
        'amount_outstanding',
        'coupon_discount',
        'coupon_codes',
        'coupon_names',
        'tickets_sent',
        'customer_notes',
        'club_notes',
    ];

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Ewave\EventGenerateReport\Model\ResourceModel\Club\Event\OrderItemFactory $orderItemCollectionFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        array $data = [])
    {
        parent::__construct($context, $backendHelper, $data);
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;
        $this->connection = $resourceConnection->getConnection();
    }

    protected function _construct()
    {
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->setUseAjax(false);
        parent::_construct();

    }

    protected function _prepareCollection()
    {
        $select = $this->connection->select()
            ->distinct()
            ->from(
                'eav_attribute',
                [
                    'attribute_code',
                    'attribute_id',
                ]
            )
            ->join(
                ['eet' => 'eav_entity_type'],
                "eav_attribute.entity_type_id = eet.entity_type_id AND eet.entity_type_code = 'catalog_product'",
                []
            )
            ->where(
                "attribute_code in (?)",
                [
                    'club_id',
                    'event_id',
                    'gl_code',
                ]
            );
        $attributeIds = $this->connection->fetchPairs($select);

        $this->_collection = $this->orderItemCollectionFactory->create();
        $this->_collection->getSelect()
            ->join(
                ['so' => 'sales_order'],
                'main_table.order_id = so.entity_id',
                [
                    'order_no' => 'so.increment_id',
                    'first_name' => 'so.customer_firstname',
                    'last_name' => 'so.customer_lastname',
                    'booking_name' => 'CONCAT(so.customer_lastname,\' \', so.customer_firstname)',
                    'gender' => '(CASE so.customer_gender WHEN 1 THEN \'Mr\' WHEN 2 THEN \'Ms\' END)',
                    'order_date' => 'so.created_at',
                    'order_edited_date' => 'so.updated_at',
                    'order_status' => 'so.state',
                    'order_item' => 'main_table.name',
                    'qty' => 'main_table.qty_ordered',
                    'product_price' => 'main_table.base_row_total',
                    'order_total' => 'so.base_grand_total',
                    'amount_paid' => 'so.total_paid',
                    'amount_outstanding' => 'so.total_paid',
                    ]
                    // TODO add left fields
            )
            ->join(
                ['cpe' => 'catalog_product_entity'],
                'main_table.product_id = cpe.entity_id',
                []
            )
            ->join(
                ['bsoa' => 'sales_order_address'],
                'so.billing_address_id = bsoa.entity_id',
                [
                    'address1' => 'bsoa.street',
                    'city' => 'bsoa.city',
                    'state' => 'bsoa.region',
                    'post_code' => 'bsoa.postcode',
                    'country' => 'bsoa.country_id',
                    'telephone' => 'bsoa.telephone',
                    'email' => 'bsoa.email',
                    'address2' => '(null)',
                ]
            )
            ->join(
                ['cpei_event' => 'catalog_product_entity_int'],
                'cpei_event.row_id = cpe.row_id AND cpei_event.attribute_id = ' . $attributeIds['event_id'],
                []
            )
            ->join(
                ['ee' => 'ewave_event'],
                'cpei_event.value = ee.id',
                [
                    'event_name' => 'ee.name',
                    'event_date' => 'ee.event_start_date',
                ]
            )
            ->join(
                ['eec' => 'ewave_event_club'],
                'eec.event_id = ee.id',
                []
            )
            ->join(
                ['ec_via_eec' => 'ewave_club'],
                'ec_via_eec.id = eec.club_id',
                []
            )
            ->joinLeft(
                ['ccev_gl_code' => 'catalog_product_entity_varchar'],
                'ccev_gl_code.row_id = cpe.row_id AND ccev_gl_code.attribute_id = ' . $attributeIds['event_id'],
                ['gl_code' => "CONCAT(ec_via_eec.gl_code,'-',ee.gl_code)"]
            );

        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        foreach ($this->fields as $field) {
            $this->addColumnById($field);
        }


        $this->addExportType('*/*/exportSalesCsv', __('CSV'));
        $this->addExportType('*/*/exportSalesExcel', __('Excel XML'));

        return parent::_prepareColumns();
    }

    protected function addColumnById($id)
    {
        $title = __(ucwords(str_replace('_', ' ', $id)));
        $this->addColumn(
            $id,
            [
                'header' => $title,
                'index' => $id,
            ]
        );
    }
}
