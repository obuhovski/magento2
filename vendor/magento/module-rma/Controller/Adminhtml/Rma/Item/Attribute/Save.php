<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Controller\Adminhtml\Rma\Item\Attribute;

class Save extends \Magento\Rma\Controller\Adminhtml\Rma\Item\Attribute
{
    /**
     * Save attribute action
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if ($this->getRequest()->isPost() && $data) {
            /* @var $attributeObject \Magento\Rma\Model\Item\Attribute */
            $attributeObject = $this->_initAttribute();
            /* @var $helper \Magento\CustomAttributeManagement\Helper\Data */
            $helper = $this->_objectManager->get('Magento\CustomAttributeManagement\Helper\Data');

            try {
                $data = $helper->filterPostData($data);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                if (isset($data['attribute_id'])) {
                    $this->_redirect('adminhtml/*/edit', ['_current' => true]);
                } else {
                    $this->_redirect('adminhtml/*/new', ['_current' => true]);
                }
                return;
            }

            $attributeId = $this->getRequest()->getParam('attribute_id');
            if ($attributeId) {
                $attributeObject->load($attributeId);
                if ($attributeObject->getEntityTypeId() != $this->_getEntityType()->getId()) {
                    $this->messageManager->addError(__('You cannot edit this attribute.'));
                    $this->_getSession()->addAttributeData($data);
                    $this->_redirect('adminhtml/*/');
                    return;
                }

                $data['attribute_code'] = $attributeObject->getAttributeCode();
                $data['frontend_input'] = $attributeObject->getFrontendInput();
                $data['is_user_defined'] = $attributeObject->getIsUserDefined();
                $data['is_system'] = $attributeObject->getIsSystem();
            } else {
                $data['backend_model'] = $helper->getAttributeBackendModelByInputType($data['frontend_input']);
                $data['source_model'] = $helper->getAttributeSourceModelByInputType($data['frontend_input']);
                $data['backend_type'] = $helper->getAttributeBackendTypeByInputType($data['frontend_input']);
                $data['entity_type_id'] = $this->_getEntityType()->getEntityTypeId();
                $data['is_user_defined'] = 1;
                $data['is_system'] = 0;

                // add set and group info
                $data['attribute_set_id'] = $this->_getEntityType()->getDefaultAttributeSetId();
                $data['attribute_group_id'] = $this->_objectManager->create(
                    'Magento\Eav\Model\Entity\Attribute\Set'
                )->getDefaultGroupId(
                    $data['attribute_set_id']
                );
            }

            if (!isset($data['used_in_forms'])) {
                $data['used_in_forms'][] = 'default';
            }

            $defaultValueField = $helper->getAttributeDefaultValueByInput($data['frontend_input']);
            if ($defaultValueField) {
                $scopeKeyPrefix = $this->getRequest()->getParam('website') ? 'scope_' : '';
                $data[$scopeKeyPrefix . 'default_value'] = $this->getRequest()->getParam(
                    $scopeKeyPrefix . $defaultValueField
                );
            }

            $data['validate_rules'] = $helper->getAttributeValidateRules($data['frontend_input'], $data);

            $attributeObject->addData($data);

            /**
             * Check "Use Default Value" checkboxes values
             */
            if ($useDefaults = $this->getRequest()->getPost('use_default')) {
                foreach ($useDefaults as $key) {
                    $attributeObject->setData('scope_' . $key, null);
                }
            }

            $attributeObject->setCanManageOptionLabels(true);

            try {
                $attributeObject->save();

                $this->messageManager->addSuccess(__('You saved the RMA item attribute.'));
                $this->_getSession()->setAttributeData(false);
                if ($this->getRequest()->getParam('back', false)) {
                    $this->_redirect(
                        'adminhtml/*/edit',
                        ['attribute_id' => $attributeObject->getId(), '_current' => true]
                    );
                } else {
                    $this->_redirect('adminhtml/*/');
                }
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_getSession()->setAttributeData($data);
                $this->_redirect('adminhtml/*/edit', ['_current' => true]);
                return;
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __('Something went wrong while saving the RMA item attribute.')
                );
                $this->_getSession()->setAttributeData($data);
                $this->_redirect('adminhtml/*/edit', ['_current' => true]);
                return;
            }
        }
        $this->_redirect('adminhtml/*/');
        return;
    }
}
