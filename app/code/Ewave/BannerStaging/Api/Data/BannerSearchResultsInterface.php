<?php

namespace Ewave\BannerStaging\Api\Data;

use Ewave\BannerStaging\Api\Data\BannerInterface;
use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for banner search results.
 * @api
 */
interface BannerSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get banners list.
     *
     * @return BannerInterface[]
     */
    public function getItems();

    /**
     * Set banners list.
     *
     * @param BannerInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
