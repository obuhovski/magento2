<?php

namespace Ewave\BannerStaging\Api;

use Ewave\BannerStaging\Api\Data\BannerInterface;
use Ewave\BannerStaging\Api\Data\BannerSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

/**
 * Banner CRUD interface.
 * @api
 */
interface BannerRepositoryInterface
{
    /**
     * Save banner.
     *
     * @param \Ewave\BannerStaging\Api\Data\BannerInterface $banner
     * @return BannerInterface
     * @throws LocalizedException
     */
    public function save(\Ewave\BannerStaging\Api\Data\BannerInterface $banner);

    /**
     * Retrieve banner.
     *
     * @param int $bannerId
     * @return BannerInterface
     * @throws LocalizedException
     */
    public function getById($bannerId);

    /**
     * Retrieve banners matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return BannerSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete banner.
     *
     * @param \Ewave\BannerStaging\Api\Data\BannerInterface $banner
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(\Ewave\BannerStaging\Api\Data\BannerInterface $banner);

    /**
     * Delete banner by ID.
     *
     * @param int $bannerId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($bannerId);
}
