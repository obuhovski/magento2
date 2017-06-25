<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogStaging\Test\Unit\Model;

use Magento\CatalogStaging\Model\VersionTables;

class VersionTablesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $metadataMock;

    public function testGetVersionTables()
    {
        $versionTables = ['test_table' => 'test_table'];
        $model = new VersionTables(['version_tables' => $versionTables]);
        $this->assertEquals($versionTables, $model->getVersionTables());
    }
}
