<?php

namespace Ewave\DynamicsCrvIntegration\Model\ResourceModel;

use Ewave\DynamicsCrvIntegration\Model\GlExport;
use Ewave\DynamicsCrvIntegration\Model\PaymentsExport;

class FinancialTransaction extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const TYPE_RETURN = 'Return';
    const TYPE_INVOICE = 'Invoice';

    /**
     * @param array $ids
     * @param string $type
     * @param array $fields
     * @return array
     */
    public function fetchEntitiesByType($ids, $type, $fields)
    {
        $isInvoiceType = $type === self::TYPE_INVOICE;
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(
                'eav_attribute', [
                                   'attribute_code',
                                   'attribute_id',
                               ]
            )
            ->join(
                ['eet' => 'eav_entity_type'],
                "eav_attribute.entity_type_id = eet.entity_type_id AND eet.entity_type_code = 'catalog_product'",
                []
            )
            ->where("attribute_code in ('club_id', 'event_id', 'name', 'gl_code')");

        $attributeIds = $connection->fetchPairs($select);

        $select = $connection->select()->from(
            ['main_table' => 'sales_invoice'],
            $fields
        )
            ->distinct()
            ->join(
                ['so' => 'sales_order'],
                'main_table.order_id = so.entity_id',
                []
            )
            ->join(
                [
                    'sub' => $connection->select()
                        ->from(
                            ['sii' => $isInvoiceType ? 'sales_invoice_item' : 'sales_creditmemo_item'],
                            [
                                'base_row_total' => 'SUM(sii.base_row_total)',
                                'base_tax_amount' => 'SUM(sii.base_tax_amount)',
                                'base_discount_amount' => 'SUM(sii.base_discount_amount)',
                                'base_row_total_incl_tax' => 'SUM(sii.base_row_total_incl_tax)',
                                'id' => 'IFNULL(soi.parent_item_id, soi.item_id)',
                                'parent_id' => 'sii.parent_id',
                            ]
                        )
                        ->join(
                            ['soi' => 'sales_order_item'],
                            'sii.order_item_id = soi.item_id',
                            []
                        )
                        ->where('sii.base_row_total_incl_tax IS NOT NULL')
                        ->group(
                            [
                                'parent_id',
                                'id',
                            ]
                        ),
                ],
                'main_table.entity_id = sub.parent_id',
                []
            )
            ->join(
                ['soi' => 'sales_order_item'],
                'so.entity_id = soi.item_id',
                []
            )
            ->join(
                ['sop' => 'sales_order_payment'],
                'so.entity_id = sop.entity_id',
                []
            )
            ->join(
                ['cpe' => 'catalog_product_entity'],
                'soi.product_id = cpe.entity_id',
                []
            )
            ->joinLeft(
                ['cpei_club' => 'catalog_product_entity_int'],
                'cpei_club.row_id = cpe.row_id AND cpei_club.attribute_id = ' . $attributeIds['club_id'],
                []
            )
            ->joinLeft(
                ['ec' => 'ewave_club'],
                'cpei_club.value = ec.id',
                []
            )
            ->joinLeft(
                ['cpei_event' => 'catalog_product_entity_int'],
                'cpei_event.row_id = cpe.row_id AND cpei_event.attribute_id = ' . $attributeIds['event_id'],
                []
            )
            ->joinLeft(
                ['ee' => 'ewave_event'],
                'cpei_event.value = ee.id',
                []
            )
            ->joinLeft(
                ['eec' => 'ewave_event_club'],
                'eec.event_id = ee.id',
                []
            )
            ->joinLeft(
                ['ec_via_eec' => 'ewave_club'],
                'ec_via_eec.id = eec.club_id',
                []
            )
            ->joinLeft(
                ['ccev_name' => 'catalog_product_entity_varchar'],
                'ccev_name.row_id = cpe.row_id AND ccev_name.attribute_id = ' . $attributeIds['name'],
                []
            )
            ->joinLeft(
                ['ccev_gl_code' => 'catalog_product_entity_varchar'],
                'ccev_gl_code.row_id = cpe.row_id AND ccev_gl_code.attribute_id = ' . $attributeIds['gl_code'],
                []
            );

        if (is_array($ids)) {
            $select->where('main_table.entity_id in (?)', $ids);

        }

        return $connection->fetchAll($select);
    }

    /**
     * @param $type
     */
    public function getNewDataIdsForGl($type)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(
                'eav_attribute', [
                                   'attribute_code',
                                   'attribute_id',
                               ]
            )
            ->join(
                ['eet' => 'eav_entity_type'],
                "eav_attribute.entity_type_id = eet.entity_type_id AND eet.entity_type_code = 'catalog_product'",
                []
            )
            ->where("attribute_code in ('member_start_date', 'club_id', 'event_id')");
        $attributeIds = $connection->fetchPairs($select);

        $select = $connection->select()
            ->from(
                ['main_table' => ($type == self::TYPE_INVOICE) ? 'sales_invoice' : 'sales_creditmemo'],
                [
                    'main_table.entity_id'
                ]
            )
            ->join(
                ['sii' => ($type == self::TYPE_INVOICE) ? 'sales_invoice_item' : 'sales_creditmemo_item'],
                'main_table.entity_id = sii.parent_id',
                []
            )
            ->join(
                ['cpe' => 'catalog_product_entity'],
                'sii.product_id = cpe.entity_id',
                []
            )
            ->joinLeft(
                ['cpei_club' => 'catalog_product_entity_int'],
                'cpei_club.row_id = cpe.row_id AND cpei_club.attribute_id = ' . $attributeIds['club_id'],
                []
            )
            ->joinLeft(
                ['ec' => 'ewave_club'],
                'cpei_club.value = ec.id',
                []
            )
            ->joinLeft(
                ['cpei_event' => 'catalog_product_entity_int'],
                'cpei_event.row_id = cpe.row_id AND cpei_event.attribute_id = ' . $attributeIds['event_id'],
                []
            )
            ->joinLeft(
                ['ee' => 'ewave_event'],
                'cpei_event.value = ee.id',
                []
            )
            ->joinLeft(
                ['cpei_msd' => 'catalog_product_entity_datetime'],
                'cpei_msd.row_id = cpe.row_id AND cpei_msd.attribute_id = ' . $attributeIds['member_start_date'],
                []
            )
            ->where('(ee.event_start_date IS NOT NULL AND DATE(ee.event_start_date) < DATE(NOW()))')
            ->orWhere('(cpei_msd.value IS NOT NULL AND DATE(cpei_msd.value) < DATE(NOW()))')
        ;
    }

    /**
     * @param $type
     * @return bool|array
     */
    public function getNewDataIdForPt($type)
    {
        $connection = $this->getConnection();
        $select = $connection->select();

        $select->from(
            [
                'ediee' => 'ewave_dynamic_integration_exported_entites_pt',
            ],
            'entity_id'
        )
            ->where('type = ?', $type)
            ->order('id DESC')
            ->limit(1);

        $lastExported = $connection->fetchCol($select);

        $select = $connection->select()
            ->from(
                ['main_table' => ($type == self::TYPE_INVOICE) ? 'sales_invoice' : 'sales_creditmemo'],
                ['entity_id']
            )->where(
                'main_table.entity_id > ?',
                $lastExported['entity_id'] ?: 0
            );

        $newIds = $connection->fetchCol($select);

        return $newIds ?: false;
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {

    }
}