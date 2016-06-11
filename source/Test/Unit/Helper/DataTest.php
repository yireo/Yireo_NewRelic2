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

        //if (!(extension_loaded('newrelic'))) {
        //    $this->markTestSkipped('The NewRelic extension is not available.');
        //}
    }

    /**
     * @test
     * @covers \Yireo\NewRelic2\Helper\Data::isEnabled
     */
    public function testIsEnabled()
    {
        $this->assertTrue($this->targetHelper->isEnabled());
    }

    /**
     * @test
     * @covers \Yireo\NewRelic2\Helper\Data::isDebug
     */
    public function testIsDebug()
    {
        $this->assertTrue($this->targetHelper->isDebug());
    }

    /**
     * @test
     * @covers \Yireo\NewRelic2\Helper\Data::getAppName
     */
    public function testGetAppName()
    {
        $this->assertEquals($this->targetHelper->getAppName(), 'dummy_appname');
    }

    /**
     * @test
     * @covers \Yireo\NewRelic2\Helper\Data::getLicense
     */
    public function testGetLicense()
    {
        $this->assertEquals($this->targetHelper->getLicense(), 'dummy_license');
    }

    /**
     * @test
     * @covers \Yireo\NewRelic2\Helper\Data::isUseXmit
     */
    public function testIsUseXmit()
    {
        $this->assertTrue($this->targetHelper->isUseXmit());
    }

    /**
     * @test
     * @covers \Yireo\NewRelic2\Helper\Data::isTrackController
     */
    public function testIsTrackController()
    {
        $this->assertTrue($this->targetHelper->isTrackController());
    }

    /**
     * @test
     * @covers \Yireo\NewRelic2\Helper\Data::isUseRUM
     */
    public function testIsUseRUM()
    {
        $this->assertTrue($this->targetHelper->isUseRUM());
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
        $bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);

        /** @var \Magento\Framework\App\Http $app */
        $bootstrap->createApplication('Magento\Framework\App\Http');

        //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        //$appState  = $objectManager->get('Magento\Framework\App\State');
        //$backendAreaCode = \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE;
        //$appState->setAreaCode($backendAreaCode);
        $appState  = $this->_getAppStateStub();

        $this->assertTrue($this->targetHelper->isAdmin());
    }

    /**
     * Get a stub for the $appState object
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getAppStateStub()
    {
        $context = $this->getMock(
            'Magento\Framework\App\State',
            ['getAreacode'],
            [\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE],
            '',
            false,
            false
        );
    }
    
    /**
     * Get a stub for the $context parameter of the helper
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getContextStub()
    {
        $scopeConfig = $this->_getScopeConfigStub();

        // @todo: Rewrite to getMockObject()
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
            'newrelic2/settings/appname' => 'dummy_appname',
            'newrelic2/settings/license' => 'dummy_license',
            'newrelic2/settings/xmit' => '1',
            'newrelic2/settings/track_controller' => '1',
            'newrelic2/settings/real_user_monitoring' => '1',
            'newrelic2/settings/fake_module' => '1',
        ];

        if (array_key_exists($hashName, $defaultConfig)) {
            return $defaultConfig[$hashName];
        }

        throw new \InvalidArgumentException('Unknown id = ' . $hashName);
    }
}
