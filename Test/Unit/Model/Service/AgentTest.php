<?php
/**
 * NewRelic2 plugin for Magento
 *
 * @package     Yireo_NewRelic2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
 * @license     Simplified BSD License
 */

namespace Yireo\NewRelic2\Test\Unit\Model\Service;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class AgentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Yireo\NewRelic2\Model\Service\Agent
     */
    protected $targetModel;

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

        $logger = $this->_getLoggerStub();
        $this->targetModel = new \Yireo\NewRelic2\Model\Service\Agent($logger);
    }

    /**
     * Get a stub for the logging part
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getLoggerStub()
    {
        $logger = $this->getMock('\Psr\Log\LoggerInterface');

        $logger->expects($this->any())->method('debug')->will($this->returnValue(true));

        return $logger;
    }

    /**
     * @test
     * @covers \Yireo\NewRelic2\Model\Service\Agent::addCustomMetric
     */
    public function testAddCustomMetric()
    {
        if ($this->targetModel->functionExists('newrelic_custom_metric')) {
            $this->assertTrue($this->targetModel->addCustomMetric('foo', 'bar'));
        } else {
            $this->assertFalse($this->targetModel->addCustomMetric('foo', 'bar'));
        }
    }

    /**
     * @test
     * @covers \Yireo\NewRelic2\Model\Service\Agent::addCustomParameter
     */
    public function testAddCustomParameter()
    {
        if ($this->targetModel->functionExists('newrelic_custom_parameter')) {
            $this->assertTrue($this->targetModel->addCustomParameter('foo', 'bar'));
        } else {
            $this->assertFalse($this->targetModel->addCustomParameter('foo', 'bar'));
        }
    }

    /**
     * @test
     * @covers \Yireo\NewRelic2\Model\Service\Agent::functionExists
     */
    public function testFunctionExists()
    {
        $this->assertTrue($this->targetModel->functionExists('get_class'));
    }
}