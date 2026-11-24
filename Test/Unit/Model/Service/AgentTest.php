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
use Yireo\NewRelic2\Helper\Data;
use Yireo\NewRelic2\Model\Service\Agent;

class AgentTest extends TestCase
{
    private Agent $targetModel;
    private LoggerInterface&MockObject $logger;
    private Data&MockObject $helper;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->helper = $this->createMock(Data::class);
        $this->targetModel = new Agent($this->logger, $this->helper);
    }

    public function testAddCustomMetric(): void
    {
        $expected = $this->targetModel->functionExists('newrelic_custom_metric');
        $this->assertSame($expected, $this->targetModel->addCustomMetric('foo', 'bar'));
    }

    public function testAddCustomParameter(): void
    {
        $expected = $this->targetModel->functionExists('newrelic_add_custom_parameter');
        $this->assertSame($expected, $this->targetModel->addCustomParameter('foo', 'bar'));
    }

    public function testFunctionExists(): void
    {
        $this->assertTrue($this->targetModel->functionExists('get_class'));
    }

    public function testNothingIsLoggedWhenDebugIsDisabled(): void
    {
        $this->helper->method('isDebug')->willReturn(false);
        $this->logger->expects($this->never())->method('debug');

        $this->targetModel->addCustomParameter('foo', 'bar');
        $this->targetModel->setAppName('app', 'secret-license', true);
    }

    public function testCallIsLoggedWhenDebugIsEnabled(): void
    {
        $this->helper->method('isDebug')->willReturn(true);
        $this->logger->expects($this->once())
            ->method('debug')
            ->with($this->stringContains('addCustomerParameter'));

        $this->targetModel->addCustomParameter('foo', 'bar');
    }

    public function testLicenseIsNeverLogged(): void
    {
        $this->helper->method('isDebug')->willReturn(true);
        $this->logger->expects($this->once())
            ->method('debug')
            ->with($this->logicalAnd(
                $this->stringContains('app'),
                $this->logicalNot($this->stringContains('secret-license'))
            ));

        $this->targetModel->setAppName('app', 'secret-license', true);
    }
}
