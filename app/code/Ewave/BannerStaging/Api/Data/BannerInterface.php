<?php

namespace Ewave\BannerStaging\Api\Data;

/**
 * Banner interface.
 * @api
 */
interface BannerInterface
{
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
     * @param $id
     * @return $this
     */
    public function setId($id);


    /**
     * @param $name
     * @return $this
     */
    public function setName($name);

    /**
     * @param $isEnabled
     * @return $this
     */
    public function setIsEnabled($isEnabled);

    /**
     * @param $isEnabled
     * @return $this
     */
    public function setIsGaEnabled($isEnabled);

    /**
     * @param $gaCreative
     * @return $this
     */
    public function setGaCreative($gaCreative);

    /**
     * @param $types
     * @return $this
     */
    public function setTypes($types);

    /**
     * @param $createdIn
     * @return $this
     */
    public function setCreatedIn($createdIn);

    /**
     * @param $updatedIn
     * @return $this
     */
    public function setUpdatedIn($updatedIn);

    /**
     * @param int
     * @return $this
     */
    public function setRowId($rowId);
}
