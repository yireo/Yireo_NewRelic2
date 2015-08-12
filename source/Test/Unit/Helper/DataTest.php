<?php
/**
 * NewRelic2 plugin for Magento
 *
 * @package     Yireo_NewRelic2
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (http://www.yireo.com/)
 * @license     Simplified BSD License
 */

namespace Yireo\NewRelic2\Test\Unit\Helper;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Yireo\NewRelic2\Helper\Data
     */
    protected $targetHelper;

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
        $this->targetHelper = new \Yireo\NewRelic2\Helper\Data($context);
    }

    /**
     * @test
     * @covers \Yireo\NewRelic2\Helper\Data::getConfigValue
     */
    public function testGetConfigValue()
    {
        $this->assertEquals($this->targetHelper->getConfigValue('enabled'), 1);
    }

    /**
     * @test
     * @covers \Yireo\NewRelic2\Helper\Data::isAdmin
     */
    public function testIsAdmin()
    {
        /*$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $appState  = $objectManager->get('Magento\Framework\App\State');

        if($appState->getAreaCode() == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE);*/
    }

    /**
     * Get a stub for the $context parameter of the helper
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getContextStub()
    {
        $scopeConfig = $this->_getScopeConfigStub();


        $context = $this->getMock(
            'Magento\Framework\App\Helper\Context',
            ['getScopeConfig'],
            [],
            '',
            false,
            false
        );

        $context->expects($this->any())->method('getScopeConfig')->will($this->returnValue($scopeConfig));

        return $context;
    }

    /**
     * Get a stub for the $scopeConfig with a $context
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getScopeConfigStub()
    {
        $scopeConfig = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');
        $scopeConfig->expects($this->any())->method('getValue')->will($this->returnCallback([$this, 'getScopeConfigMethodStub']));

        return $scopeConfig;
    }

    /**
     * Mimic configuration values for usage within $scopeConfig
     *
     * @param $hashName
     *
     * @return mixed
     */
    public function getScopeConfigMethodStub($hashName)
    {
        $defaultConfig = [
            'newrelic2/settings/enabled' => '1',
        ];

        if (array_key_exists($hashName, $defaultConfig)) {
            return $defaultConfig[$hashName];
        }

        throw new \InvalidArgumentException('Unknow id = ' . $hashName);
    }
}