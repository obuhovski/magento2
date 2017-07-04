<?php
/**
 * Relations between a banner and customer segments
 */
namespace Ewave\BannerStaging\Model\ResourceModel;

class BannerSegmentLink extends \Magento\BannerCustomerSegment\Model\ResourceModel\BannerSegmentLink
{

    /**
     * @inheritdoc
     */
    public function loadBannerSegments($rowId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getMainTable(),
            'segment_id'
        )->where(
            'row_id = ?',
            $rowId
        );
        return $this->getConnection()->fetchCol($select);
    }

    /**
     * @inheritdoc
     */
    public function saveBannerSegments($rowId, array $segmentIds)
    {
        foreach ($segmentIds as $segmentId) {
            $this->getConnection()->insertOnDuplicate(
                $this->getMainTable(),
                ['row_id' => $rowId, 'segment_id' => $segmentId],
                ['row_id']
            );
        }
        if (!$segmentIds) {
            $segmentIds = [0];
        }
        $this->getConnection()->delete(
            $this->getMainTable(),
            ['row_id = ?' => $rowId, 'segment_id NOT IN (?)' => $segmentIds]
        );
    }

    /**
     * @inheritdoc
     */
    public function addBannerSegmentFilter(\Magento\Framework\DB\Select $select, array $segmentIds)
    {
        $select->joinLeft(
            ['banner_segment' => $this->getMainTable()],
            'banner_segment.row_id = main_table.row_id',
            []
        );
        if ($segmentIds) {
            $select->where('banner_segment.segment_id IS NULL OR banner_segment.segment_id IN (?)', $segmentIds);
        } else {
            $select->where('banner_segment.segment_id IS NULL');
        }
    }
}
