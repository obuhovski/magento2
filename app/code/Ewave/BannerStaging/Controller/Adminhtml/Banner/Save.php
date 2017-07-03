<?php

namespace Ewave\BannerStaging\Controller\Adminhtml\Banner;

class Save extends \Magento\Banner\Controller\Adminhtml\Banner\Save
{
    /**
     * Load Banner from request
     *
     * @param string $idFieldName
     * @return \Magento\Banner\Model\Banner $model
     */
    protected function _initBanner($idFieldName = 'row_id')
    {
        $bannerId = (int)$this->getRequest()->getParam($idFieldName);
        $model = $this->_objectManager->create('Ewave\BannerStaging\Api\Data\BannerInterface');
        if ($bannerId) {
            $model->load($bannerId);
        }
        if (!$this->_registry->registry('current_banner')) {
            $this->_registry->register('current_banner', $model);
        }
        return $model;
    }
}
