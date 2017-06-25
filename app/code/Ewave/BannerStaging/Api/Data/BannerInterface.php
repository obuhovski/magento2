<?php

namespace Ewave\BannerStaging\Api\Data;

/**
 * CMS page interface.
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
     * @return $this
     */
    public function setId($id);


    /**
     * @return $this
     */
    public function setName($name);

    /**
     * @return $this
     */
    public function setIsEnabled($isEnabled);

    /**
     * @return $this
     */
    public function setIsGaEnabled($isEnabled);

    /**
     * @return $this
     */
    public function setGaCreative($gaCreative);

    /**
     * @return $this
     */
    public function setTypes($types);

    /**
     * @return $this
     */
    public function setCreatedIn($createdIn);

    /**
     * @return $this
     */
    public function setUpdatedIn($updatedIn);
}
