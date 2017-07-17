<?php

namespace Ewave\BannerStaging\Api;

use Ewave\BannerStaging\Api\Data\BannerInterface;
use Ewave\BannerStaging\Api\Data\BannerSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Banner CRUD interface.
 * @api
 */
interface BannerRepositoryInterface
{
    /**
     * Save banner.
     *
     * @param BannerInterface $banner
     * @return BannerInterface
     * @throws LocalizedException
     */
    public function save(BannerInterface $banner);

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
    public function getList(SearchCriteriaInterface $searchCriteria = null);

    /**
     * Retrieve banners matching the specified criteria.
     *
     * @param int[] $ids
     * @return BannerSearchResultsInterface
     * @internal param SearchCriteriaInterface $searchCriteria
     */
    public function getListByRowIds(array $ids);

    /**
     * Delete banner.
     *
     * @param BannerInterface $banner
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(BannerInterface $banner);

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
