<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GiftCard\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Setup\CategorySetupFactory;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * Constructor
     *
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(CategorySetupFactory $categorySetupFactory)
    {
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
        // 0.0.2 => 0.0.3
        $categorySetup->addAttribute(
            'catalog_product',
            'giftcard_amounts',
            [
                'group' => 'Advanced Pricing',
                'type' => 'decimal',
                'backend' => 'Magento\GiftCard\Model\Attribute\Backend\Giftcard\Amount',
                'frontend' => '',
                'label' => 'Amounts',
                'input' => 'price',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'unique' => false,
                'apply_to' => 'giftcard',
                'used_in_product_listing' => true,
                'sort_order' => -5
            ]
        );

        $categorySetup->addAttribute(
            'catalog_product',
            'allow_open_amount',
            [
                'group' => 'Advanced Pricing',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Allow Open Amount',
                'input' => 'select',
                'input_renderer' => 'Magento\GiftCard\Block\Adminhtml\Renderer\OpenAmount',
                'class' => '',
                'source' => 'Magento\GiftCard\Model\Source\Open',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'unique' => false,
                'apply_to' => 'giftcard',
                'used_in_product_listing' => true,
                'sort_order' => -4
            ]
        );
        $categorySetup->addAttribute(
            'catalog_product',
            'open_amount_min',
            [
                'group' => 'Advanced Pricing',
                'type' => 'decimal',
                'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Price',
                'frontend' => '',
                'label' => 'Open Amount Min Value',
                'input' => 'price',
                'class' => 'validate-number',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'unique' => false,
                'apply_to' => 'giftcard',
                'used_in_product_listing' => true,
                'sort_order' => -3
            ]
        );
        $categorySetup->addAttribute(
            'catalog_product',
            'open_amount_max',
            [
                'group' => 'Advanced Pricing',
                'type' => 'decimal',
                'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Price',
                'frontend' => '',
                'label' => 'Open Amount Max Value',
                'input' => 'price',
                'class' => 'validate-number',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'unique' => false,
                'apply_to' => 'giftcard',
                'used_in_product_listing' => true,
                'sort_order' => -2
            ]
        );

        $categorySetup->addAttribute(
            'catalog_product',
            'giftcard_type',
            [
                'group' => 'Advanced Pricing',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Card Type',
                'input' => 'select',
                'class' => '',
                'source' => 'Magento\GiftCard\Model\Source\Type',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => true,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'unique' => false,
                'apply_to' => 'giftcard'
            ]
        );

        $categorySetup->addAttribute(
            'catalog_product',
            'is_redeemable',
            [
                'group' => 'Advanced Pricing',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Is Redeemable',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'visible' => false,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'unique' => false,
                'apply_to' => 'giftcard'
            ]
        );

        $categorySetup->addAttribute(
            'catalog_product',
            'use_config_is_redeemable',
            [
                'group' => 'Advanced Pricing',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Use Config Is Redeemable',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'visible' => false,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'unique' => false,
                'apply_to' => 'giftcard'
            ]
        );

        $categorySetup->addAttribute(
            'catalog_product',
            'lifetime',
            [
                'group' => 'Advanced Pricing',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Lifetime',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'visible' => false,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'unique' => false,
                'apply_to' => 'giftcard'
            ]
        );

        $categorySetup->addAttribute(
            'catalog_product',
            'use_config_lifetime',
            [
                'group' => 'Advanced Pricing',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Use Config Lifetime',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'visible' => false,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'unique' => false,
                'apply_to' => 'giftcard'
            ]
        );

        $categorySetup->addAttribute(
            'catalog_product',
            'email_template',
            [
                'group' => 'Advanced Pricing',
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Email Template',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible' => false,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'unique' => false,
                'apply_to' => 'giftcard'
            ]
        );

        $categorySetup->addAttribute(
            'catalog_product',
            'use_config_email_template',
            [
                'group' => 'Advanced Pricing',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Use Config Email Template',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible' => false,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'unique' => false,
                'apply_to' => 'giftcard'
            ]
        );
        // 0.0.3 => 0.0.4
        $categorySetup->addAttribute(
            'catalog_product',
            'allow_message',
            [
                'group' => 'Advanced Pricing',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Allow Message',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible' => false,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'unique' => false,
                'apply_to' => 'giftcard'
            ]
        );

        $categorySetup->addAttribute(
            'catalog_product',
            'use_config_allow_message',
            [
                'group' => 'Advanced Pricing',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Use Config Allow Message',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible' => false,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'unique' => false,
                'apply_to' => 'giftcard'
            ]
        );

        // 0.0.4 => 0.0.5 make 'weight' attribute applicable to gift card products
        $applyTo = $categorySetup->getAttribute('catalog_product', 'weight', 'apply_to');
        if ($applyTo) {
            $applyTo = explode(',', $applyTo);
            if (!in_array('giftcard', $applyTo)) {
                $applyTo[] = 'giftcard';
                $categorySetup->updateAttribute('catalog_product', 'weight', 'apply_to', join(',', $applyTo));
            }
        }

        // 0.0.6 => 0.0.7
        $fieldList = ['cost'];

        // make these attributes not applicable to gift card products
        foreach ($fieldList as $field) {
            $applyTo = explode(',', $categorySetup->getAttribute('catalog_product', $field, 'apply_to'));
            if (in_array('giftcard', $applyTo)) {
                foreach ($applyTo as $k => $v) {
                    if ($v == 'giftcard') {
                        unset($applyTo[$k]);
                        break;
                    }
                }
                $categorySetup->updateAttribute('catalog_product', $field, 'apply_to', join(',', $applyTo));
            }
        }

        $groupName = 'Product Details';
        $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $attributeSetId = $categorySetup->getAttributeSetId($entityTypeId, 'Default');

        $attributesOrder = [
            'giftcard_type' => 31,
            'giftcard_amounts' => 32,
            'allow_open_amount' => 33,
            'open_amount_min' => 34,
            'open_amount_max' => 35,
        ];

        foreach ($attributesOrder as $key => $order) {
            $attribute = $categorySetup->getAttribute($entityTypeId, $key);
            if ($attribute) {
                $categorySetup->addAttributeToGroup(
                    $entityTypeId,
                    $attributeSetId,
                    $groupName,
                    $attribute['attribute_id'],
                    $order
                );
            }
        }

    }
}
