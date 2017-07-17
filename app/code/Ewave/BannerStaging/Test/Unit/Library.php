<?php
namespace Ewave\BannerStaging\Test\Unit;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

// @codingStandardsIgnoreFile
/**
 * Class Library
 *
 * @package Ewave\Banner\Test\Unit
 */
class Library extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Setup object manager
     */
    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
    }

    /**
     * @param string $className
     * @param array $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function getMockObjectWithoutConstructor($className, array $methods = [])
    {
        return $this->getMock($className, $methods, [], '', false);
    }
}
