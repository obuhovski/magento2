<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftCardAccount\Controller\Adminhtml\Giftcardaccount;

use Magento\GiftCardAccount\Model\Giftcardaccount;

class Save extends \Magento\GiftCardAccount\Controller\Adminhtml\Giftcardaccount
{
    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array $data
     * @return array
     */
    protected function _filterPostData($data)
    {
        if (!empty($data['date_expires'])) {
            $data['date_expires'] = $this->_dateFilter->filter($data['date_expires']);
        }

        return $data;
    }

    /**
     * Save action
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        // check if data sent
        $data = $this->getRequest()->getPostValue();
        $id = $this->getRequest()->getParam('giftcardaccount_id');

        try {
            $data = $this->_filterPostData($data);
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            unset($data['date_expires']);
            $this->_getSession()->setFormData($data);
            $this->_redirect('adminhtml/*/edit', ['id' => $id]);
            return;
        }

        // init model and set data
        $model = $this->_initGca('giftcardaccount_id');
        if (!$model->getId() && $id) {
            $this->messageManager->addError(__('This gift card account has been deleted.'));
            $this->_redirect('adminhtml/*/');
            return;
        }

        if ($this->_objectManager->get('Magento\Store\Model\StoreManager')->isSingleStoreMode()) {
            $data['website_id'] = $this->_objectManager->get(
                'Magento\Store\Model\StoreManager'
            )->getStore(
                true
            )->getWebsiteId();
        }

        if (!empty($data)) {
            $model->addData($data);
        }

        // try to save it
        try {
            $model->save();
            if ($model->getSendAction()) {
                if ($model->getStatus()) {
                    $this->sendEmail($model);
                } else {
                    $this->messageManager->addSuccess(__('You saved the gift card account.'));
                    $this->messageManager->addNotice(
                        __('An email was not sent because the gift card account is not active.')
                    );
                }
            } else {
                $this->messageManager->addSuccess(__('You saved the gift card account.'));
            }

            // clear previously saved data from session
            $this->_getSession()->setFormData(false);

            // check if 'Save and Continue'
            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('adminhtml/*/edit', ['id' => $model->getId()]);
                return;
            }
        } catch (\Exception $e) {
            // display error message
            $this->messageManager->addError($e->getMessage());
            // save data in session
            $this->_getSession()->setFormData($data);
            // redirect to edit form
            $this->_redirect('adminhtml/*/edit', ['id' => $model->getId()]);
            return;
        }

        $this->_redirect('adminhtml/*/');
    }

    /**
     * Send email to customer
     *
     * @param Giftcardaccount $model
     * @return void
     */
    private function sendEmail(Giftcardaccount $model)
    {
        $model->sendEmail();
        if ($model->getEmailSent()) {
            $this->messageManager->addSuccess(__('You saved the gift card account.'));
        } else {
            $this->messageManager->addError(
                __('You saved the gift card account, but an email was not sent.')
            );
        }
    }
}
