<?php

namespace Ewave\Test\Unit\Model\Banner;

use Ewave\BannerStaging\Model\Banner;
use Ewave\BannerStaging\Model\Banner\Hydrator;
use Magento\Framework\EntityManager\EntityMetadataInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Event\ManagerInterface;
use Magento\Staging\Model\Entity\RetrieverInterface;

// @codingStandardsIgnoreFile

/**
 * Class HydratorTest
 *
 * @package Ewave\Banner\Test\Unit\Model
 */
class HydratorTest extends \Ewave\BannerStaging\Test\Unit\Library
{
    /**
     * @var Hydrator
     */
    protected $hydratorOriginal;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $retrieverMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $managerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $metadataPoolMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityMetadataMock;

    /**
     * Setup objects
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->retrieverMock = $this->getMockObjectWithoutConstructor(RetrieverInterface::class);
        $this->entityMetadataMock = $this->getMockObjectWithoutConstructor(EntityMetadataInterface::class);
        $this->metadataPoolMock = $this->getMockObjectWithoutConstructor(MetadataPool::class);
        $this->managerMock = $this->getMockObjectWithoutConstructor(ManagerInterface::class);

        $this->hydratorOriginal = $this->objectManager->getObject(
            Hydrator::class, [
                'entityRetriever' => $this->retrieverMock,
                'metadataPool' => $this->metadataPoolMock
            ]
        );
    }

    /**
     * Test hydrate
     *
     * @return void
     */
    public function testHydrate()
    {
        $banner = $this->objectManager->getObject(Banner::class);
        $banner->setData(['banner_id' => 1]);

        $this->managerMock
            ->expects($this->any())
            ->method('getEntity')
            ->willReturn(null);

        $this->entityMetadataMock
            ->expects($this->any())
            ->method('getLinkField')
            ->willReturn('row_id');

        $this->metadataPoolMock
            ->expects($this->any())
            ->method('getMetadata')
            ->willReturn($this->entityMetadataMock);

        $this->retrieverMock
            ->expects($this->any())
            ->method('getEntity')
            ->willReturn($banner);

        $this->assertEquals([
            'banner_id' => 1,
            'is_enabled' => 1,
            'row_id' => NULL,
            'created_in' => NULL,
            'updated_in' => NULL,
        ],
            $this->hydratorOriginal->hydrate([
                'banner_id' => 1,
                'is_enabled' => 'true',
            ])->getData()
        );
    }
}
