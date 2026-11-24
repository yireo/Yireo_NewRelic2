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

namespace Yireo\NewRelic2\Test\Unit\Block\Rum\Timing;

use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Yireo\NewRelic2\Block\Rum\Timing\Generic;
use Yireo\NewRelic2\Helper\Data;

/**
 * @covers \Yireo\NewRelic2\Block\Rum\Timing\Generic
 */
class GenericTest extends TestCase
{
    /**
     * @var Generic
     */
    protected $targetBlock;

    /**
     * Setup method
     */
    protected function setUp(): void
    {
        $this->targetBlock = new Generic($this->getContextStub(), $this->getHelperStub(), $this->createMock(SecureHtmlRenderer::class), []);
    }

    public function testGetContentHtml()
    {
        $this->assertSame('', $this->targetBlock->getContentHtml());
    }

    public function testCanShow()
    {
        $method = (new ReflectionClass(Generic::class))->getMethod('_canShow');
        $result = $method->invoke($this->targetBlock);

        $this->assertTrue($result);
    }

    public function testRenderScriptUsesSecureHtmlRenderer()
    {
        $secureHtmlRenderer = $this->createMock(SecureHtmlRenderer::class);
        $secureHtmlRenderer->expects($this->once())
            ->method('renderTag')
            ->with('script', [], 'window.NREUM={};', false)
            ->willReturn('<script>window.NREUM={};</script>');

        $block = new Generic($this->getContextStub(), $this->getHelperStub(), $secureHtmlRenderer, []);
        $method = (new ReflectionClass(Generic::class))->getMethod('renderScript');

        $this->assertSame('<script>window.NREUM={};</script>', $method->invoke($block, 'window.NREUM={};'));
    }

    public function testRenderScriptReturnsEmptyStringWithoutJavaScript()
    {
        $secureHtmlRenderer = $this->createMock(SecureHtmlRenderer::class);
        $secureHtmlRenderer->expects($this->never())->method('renderTag');

        $block = new Generic($this->getContextStub(), $this->getHelperStub(), $secureHtmlRenderer, []);
        $method = (new ReflectionClass(Generic::class))->getMethod('renderScript');

        $this->assertSame('', $method->invoke($block, ' '));
    }

    /**
     * @return Data&MockObject
     */
    protected function getHelperStub()
    {
        $helper = $this->createMock(Data::class);
        $helper->method('isEnabled')->willReturn(true);
        $helper->method('isUseRUM')->willReturn(true);

        return $helper;
    }

    /**
     * @return Context&MockObject
     */
    protected function getContextStub()
    {
        return $this->createMock(Context::class);
    }
}
