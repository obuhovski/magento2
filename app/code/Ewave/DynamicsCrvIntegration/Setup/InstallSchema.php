<?php
namespace Ewave\DynamicsCrvIntegration\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\DB\Ddl\Table;
use Ewave\Club\Model\ResourceModel\Club;
use Ewave\Club\Api\Data\ClubInterface;

/**
 * Class InstallSchema
 * @package Ewave\Club\Setup
 *
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $this->createEwaveDynamicIntegrationExportedEntitesTable($setup);

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    public function createEwaveDynamicIntegrationExportedEntitesTable(SchemaSetupInterface $setup)
    {
        return $this;
    }
}
