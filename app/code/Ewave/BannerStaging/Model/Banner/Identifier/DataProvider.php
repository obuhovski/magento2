<?php

namespace Ewave\BannerStaging\Model\Banner\Identifier;

use Magento\Cms\Model\Page;
use Magento\Cms\Model\Page\DataProvider as CmsDataProvider;

/**
 * Class DataProvider
 */
class DataProvider extends CmsDataProvider
{
    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var Page $page */
        foreach ($items as $page) {
            $this->loadedData[$page->getId()] = [
                'page_id' => $page->getId(),
                'title' => $page->getTitle(),
            ];
        }

        return $this->loadedData;
    }
}
