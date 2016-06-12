<?php
/**
 * NewRelic2 plugin for Magento
 *
 * @package     Yireo_NewRelic2
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (http://www.yireo.com/)
 * @license     Simplified BSD License
 */

namespace Yireo\NewRelic2\Test\Unit\Block\Rum\Timing;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class GenericTest
 *
 * @package Yireo\NewRelic2\Test\Unit\Block\Rum\Timing
 */
class GenericTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Yireo\NewRelic2\Block\Rum\Timing\Generic
     */
    protected $targetBlock;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * Setup method
     */
    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $context = $this->_getContextStub();
        $helper = $this->_getHelperStub();
        $data = [];

        $this->targetBlock = new \Yireo\NewRelic2\Block\Rum\Timing\Generic($context, $data, $helper);
    }

    /**
     * @test
     * @covers \Yireo\NewRelic2\Block\Rum\Timing\Generic::getContentHtml
     */
    public function testCanShow()
    {
        $context = $this->_getContextStub();
        $helper = $this->_getHelperStub();
        $data = [];
        $instanceArgs = [$context, $data, $helper];

        $class = new \ReflectionClass('Yireo\NewRelic2\Block\Rum\Timing\Generic');
        $instance = $class->newInstanceArgs($instanceArgs);
        $method = $class->getMethod('_canShow');
        $method->setAccessible(true);
        $result = $method->invokeArgs($this->targetBlock, []);

        $this->assertTrue($result);
    }
    
    /**
     * Get a stub for the helper
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getHelperStub()
    {
        $helper = $this->getMockBuilder('Yireo\NewRelic2\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();

        $helper->method('isEnabled')
             ->willReturn(true);

        $helper->method('isUseRUM')
             ->willReturn(true);

        return $helper;
    }

    /**
     * Get a stub for the context
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getContextStub()
    {
        $context = $this->getMockBuilder('Magento\Framework\View\Element\Context')
            ->disableOriginalConstructor()
            ->getMock();

        return $context;
    }
}
