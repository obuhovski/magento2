<?php

namespace Ewave\BannerStaging\Api\Data;

/**
 * CMS page interface.
 * @api
 */
interface BannerInterface
{
    public function getId();

    public function getName();

    public function getIsEnabled();

    public function getTypes();

    public function getIsGaCreative();

    public function getCreatedIn();

    public function getUpdatedIn();

    public function setId($id);

    public function setName($name);

    public function setIsEnabled($isEnabled);

    public function setTypes($types);

    public function setIsGaCreative($isGaCreative);

    public function setCreatedIn($createdIn);

    public function setUpdatedIn($updatedIn);
}
