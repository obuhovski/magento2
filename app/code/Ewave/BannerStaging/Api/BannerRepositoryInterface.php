<?php

namespace Ewave\BannerStaging\Api;

use Ewave\BannerStaging\Api\Data\BannerInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

/**
 * CMS page CRUD interface.
 * @api
 */
interface BannerRepositoryInterface
{
    /**
     * Save page.
     *
     * @param \Ewave\BannerStaging\Api\Data\BannerInterface $page
     * @return BannerInterface
     * @throws LocalizedException
     */
    public function save(\Ewave\BannerStaging\Api\Data\BannerInterface $page);

    /**
     * Retrieve page.
     *
     * @param int $pageId
     * @return BannerInterface
     * @throws LocalizedException
     */
    public function getById($pageId);

    /**
     * Retrieve pages matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return PageSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete page.
     *
     * @param \Ewave\BannerStaging\Api\Data\BannerInterface $page
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(\Ewave\BannerStaging\Api\Data\BannerInterface $page);

    /**
     * Delete page by ID.
     *
     * @param int $pageId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($pageId);
}
