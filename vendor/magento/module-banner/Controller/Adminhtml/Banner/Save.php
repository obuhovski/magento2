<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Banner\Controller\Adminhtml\Banner;

class Save extends \Magento\Banner\Controller\Adminhtml\Banner
{
    /**
     * @var \Magento\Banner\Model\Banner\Validator
     */
    protected $bannerValidator;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Banner\Model\Banner\Validator $bannerValidator
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Banner\Model\Banner\Validator $bannerValidator
    ) {
        $this->bannerValidator = $bannerValidator;
        parent::__construct($context, $registry);
    }

    /**
     * Save action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $redirectBack = $this->getRequest()->getParam('back', false);
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            return $resultRedirect->setPath('adminhtml/*/');
        }

        /** @var \Magento\Banner\Model\Banner $model */
        $model = $this->_initBanner();
        if (!$this->isBannerExist($model)) {
            $this->messageManager->addError(__('This banner does not exist.'));
            return $resultRedirect->setPath('adminhtml/*/');
        }

        $data = $this->bannerValidator->prepareSaveData($data);

        try {
            $this->prepareBannerModelData($model, $data);
            $model->save();
            $this->_getSession()->setFormData(false);
            $this->messageManager->addSuccess(__('You saved the banner.'));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $redirectBack = true;
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $redirectBack = true;
            $this->messageManager->addError(__('We cannot save the banner.'));
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
        }

        return ($redirectBack)
            ? $resultRedirect->setPath('adminhtml/*/edit', ['id' => $model->getId()])
            : $resultRedirect->setPath('adminhtml/*/');
    }

    /**
     * Check if banner exist
     *
     * @param \Magento\Banner\Model\Banner $model
     * @return bool
     */
    protected function isBannerExist(\Magento\Banner\Model\Banner $model)
    {
        $bannerId = $this->getRequest()->getParam('id');
        return (!$model->getId() && $bannerId) ? false : true;
    }

    /**
     * Prepare banner model data
     *
     * @param \Magento\Banner\Model\Banner $model
     * @param array $data
     * @return void
     */
    protected function prepareBannerModelData(\Magento\Banner\Model\Banner $model, array $data)
    {
        if (!empty($data)) {
            $model->addData($data);
            $this->_getSession()->setFormData($data);
        }
    }
}
