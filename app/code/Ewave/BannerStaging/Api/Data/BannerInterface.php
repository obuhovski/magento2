<?php

namespace Ewave\BannerStaging\Api\Data;

/**
 * Banner interface.
 * @api
 */
interface BannerInterface
{
    const BANNER_ID = 'banner_id';
    const NAME = 'name';
    const IS_ENABLED = 'is_enabled';
    const IS_GA_ENABLED = 'is_ga_enabled';
    const TYPES = 'types';
    const GA_CREATIVE = 'ga_creative';
    const CREATED_IN = 'created_in';
    const UPDATED_IN = 'updated_in';
    const ROW_ID = 'row_id';
    const STORE_CONTENTS = 'store_contents';
    const RELATED_SALES_RULE = 'related_sales_rule';
    const RELATED_CATALOG_RULE = 'related_catalog_rule';
    const CUSTOMER_SEGMENT_IDS = 'customer_segment_ids';
    const APPLIES_TO = 'applies_to';
    const CUSTOMER_SEGMENTS = 'customer_segments';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get row ID
     *
     * @return int|null
     */
    public function getRowId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return boolean
     */
    public function getIsEnabled();

    /**
     * @return boolean
     */
    public function getIsGaEnabled();

    /**
     * @return string
     */
    public function getGaCreative();

    /**
     * @return integer
     */
    public function getCreatedIn();

    /**
     * @return integer
     */
    public function getUpdatedIn();

    /**
     * @param integer $id
     * @return $this
     */
    public function setId($id);

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @param boolean $isEnabled
     * @return $this
     */
    public function setIsEnabled($isEnabled);

    /**
     * @param boolean $isEnabled
     * @return $this
     */
    public function setIsGaEnabled($isEnabled);

    /**
     * @param string $gaCreative
     * @return $this
     */
    public function setGaCreative($gaCreative);

    /**
     * @param string $types
     * @return $this
     */
    public function setTypes($types);

    /**
     * @param integer $createdIn
     * @return $this
     */
    public function setCreatedIn($createdIn);

    /**
     * @param integer $updatedIn
     * @return $this
     */
    public function setUpdatedIn($updatedIn);

    /**
     * @param int $rowId
     * @return $this
     */
    public function setRowId($rowId);
}
