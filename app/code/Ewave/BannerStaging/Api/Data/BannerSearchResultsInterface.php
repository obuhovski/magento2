<?php

namespace Ewave\BannerStaging\Api\Data;

use Ewave\BannerStaging\Api\Data\BannerInterface;
use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for cms page search results.
 * @api
 */
interface BannerSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get pages list.
     *
     * @return BannerInterface[]
     */
    public function getItems();

    /**
     * Set pages list.
     *
     * @param BannerInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
