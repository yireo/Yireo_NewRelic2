<?php
/**
 * NewRelic2 plugin for Magento
 *
 * @package     Yireo_NewRelic2
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (http://www.yireo.com/)
 * @license     Simplified BSD License
 */
declare(strict_types=1);

namespace Yireo\NewRelic2\Test\Unit\Helper;

use InvalidArgumentException;
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\State;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Yireo\NewRelic2\Helper\Data;

/**
 * Class DataTest
 *
 * @package Yireo\NewRelic2\Test\Unit\Helper
 */
class DataTest extends TestCase
{
    /**
     * @var Data
     */
    protected $targetHelper;

    /**
     * Setup method
     */
    protected function setUp(): void
    {
        $context = $this->_getContextStub();
        $appState = $this->_getAppStateStub();
        $this->targetHelper = new Data($context, $appState);
    }

    /**
     * @covers \Yireo\NewRelic2\Helper\Data::isEnabled
     */
    public function testIsEnabled()
    {
        $this->assertTrue($this->targetHelper->isEnabled());
    }

    /**
     * @covers \Yireo\NewRelic2\Helper\Data::isDebug
     */
    public function testIsDebug()
    {
        $this->assertTrue($this->targetHelper->isDebug());
    }

    /**
     * @covers \Yireo\NewRelic2\Helper\Data::getAppName
     */
    public function testGetAppName()
    {
        $this->assertEquals($this->targetHelper->getAppName(), 'dummy_appname');
    }

    /**
     * @covers \Yireo\NewRelic2\Helper\Data::getLicense
     */
    public function testGetLicense()
    {
        $this->assertEquals($this->targetHelper->getLicense(), 'dummy_license');
    }

    /**
     * @covers \Yireo\NewRelic2\Helper\Data::isUseXmit
     */
    public function testIsUseXmit()
    {
        $this->assertTrue($this->targetHelper->isUseXmit());
    }

    /**
     * @covers \Yireo\NewRelic2\Helper\Data::isTrackController
     */
    public function testIsTrackController()
    {
        $this->assertTrue($this->targetHelper->isTrackController());
    }

    /**
     * @covers \Yireo\NewRelic2\Helper\Data::isUseRUM
     */
    public function testIsUseRUM()
    {
        $this->assertTrue($this->targetHelper->isUseRUM());
    }

    /**
     * @covers \Yireo\NewRelic2\Helper\Data::getConfigValue
     */
    public function testGetConfigValue()
    {
        $this->assertEquals($this->targetHelper->getConfigValue('enabled'), 1);
    }

    /**
     * @covers \Yireo\NewRelic2\Helper\Data::isAdmin
     */
    public function testIsAdmin()
    {
        $this->assertTrue($this->targetHelper->isAdmin());
    }

    /**
     * Get a stub for the $appState object
     *
     * @return MockObject
     */
    protected function _getAppStateStub()
    {
        $appState = $this->createMock(State::class);
        $appState->method('getAreaCode')->willReturn(FrontNameResolver::AREA_CODE);

        return $appState;
    }

    /**
     * Get a stub for the $context parameter of the helper
     *
     * @return MockObject
     */
    protected function _getContextStub()
    {
        $scopeConfig = $this->_getScopeConfigStub();

        $context = $this->createMock(Context::class);
        $context->method('getScopeConfig')->willReturn($scopeConfig);

        return $context;
    }

    /**
     * Get a stub for the $scopeConfig with a $context
     *
     * @return MockObject
     */
    protected function _getScopeConfigStub()
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);

        $scopeConfig->method('getValue')->willReturnCallback([$this, 'getScopeConfigMethodStub']);

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
            'newrelic2/settings/debug' => '1',
        ];

        if (array_key_exists($hashName, $defaultConfig)) {
            return $defaultConfig[$hashName];
        }

        throw new InvalidArgumentException('Unknown id = ' . $hashName);
    }
}
