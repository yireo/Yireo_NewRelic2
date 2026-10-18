<?php
/**
 * NewRelic2 plugin for Magento
 *
 * @package     Yireo_NewRelic2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
 * @license     Simplified BSD License
 */
declare(strict_types=1);

namespace Yireo\NewRelic2\Test\Unit\Model\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Yireo\NewRelic2\Model\Service\Agent;

class AgentTest extends TestCase
{
    /**
     * @var Agent
     */
    protected $targetModel;

    /**
     * Setup method
     */
    protected function setUp(): void
    {
        $logger = $this->_getLoggerStub();
        $this->targetModel = new Agent($logger);
    }

    /**
     * Get a stub for the logging part
     *
     * @return MockObject
     */
    protected function _getLoggerStub()
    {
        return $this->createMock(LoggerInterface::class);
    }

    /**
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
     * @covers \Yireo\NewRelic2\Model\Service\Agent::addCustomParameter
     */
    public function testAddCustomParameter()
    {
        if ($this->targetModel->functionExists('newrelic_add_custom_parameter')) {
            $this->assertTrue($this->targetModel->addCustomParameter('foo', 'bar'));
        } else {
            $this->assertFalse($this->targetModel->addCustomParameter('foo', 'bar'));
        }
    }

    /**
     * @covers \Yireo\NewRelic2\Model\Service\Agent::functionExists
     */
    public function testFunctionExists()
    {
        $this->assertTrue($this->targetModel->functionExists('get_class'));
    }
}