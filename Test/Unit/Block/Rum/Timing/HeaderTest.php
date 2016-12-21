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

class HeaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Yireo\NewRelic2\Block\Rum\Timing\Header
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

        $this->targetBlock = new \Yireo\NewRelic2\Block\Rum\Timing\Header($context, $data, $helper);
    }

    /**
     * @test
     * @covers \Yireo\NewRelic2\Block\Rum\Timing\Header::getContentHtml
     */
    public function testGetContentHtml()
    {
        $this->assertEmpty($this->targetBlock->getContentHtml());
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
