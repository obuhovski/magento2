<?php

namespace Ewave\BannerStaging\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Staging\Setup\BasicSetup;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var BasicSetup
     */
    protected $basicSetup;

    /**
     * @param BasicSetup $basicSetup
     */
    public function __construct(BasicSetup $basicSetup)
    {
        $this->basicSetup = $basicSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $this->basicSetup->install(
            $setup,
            'sequence_magento_banner',
            'magento_banner',
            'banner_id',
            [
                [
                    'referenceTable' => 'magento_banner_catalogrule',
                    'referenceColumn' => 'banner_id',
                    'staged' => true,
                ],
                [
                    'referenceTable' => 'magento_banner_content',
                    'referenceColumn' => 'banner_id',
                    'staged' => true,
                ],
                [
                    'referenceTable' => 'magento_banner_customersegment',
                    'referenceColumn' => 'banner_id',
                    'staged' => true,
                ],
                [
                    'referenceTable' => 'magento_banner_salesrule',
                    'referenceColumn' => 'banner_id',
                    'staged' => true,
                ]
            ]
        );

        $installer->endSetup();
    }
}
