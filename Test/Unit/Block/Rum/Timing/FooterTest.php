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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Yireo\NewRelic2\Block\Rum\Timing\Footer;
use Yireo\NewRelic2\Helper\Data;

/**
 * @covers \Yireo\NewRelic2\Block\Rum\Timing\Footer
 */
class FooterTest extends TestCase
{
    /**
     * @var Footer
     */
    protected $targetBlock;

    /**
     * Setup method
     */
    protected function setUp(): void
    {
        $this->targetBlock = new Footer($this->getContextStub(), $this->getHelperStub(), []);
    }

    public function testGetContentHtml()
    {
        $this->assertEmpty($this->targetBlock->getContentHtml());
    }

    /**
     * @return Data&MockObject
     */
    protected function getHelperStub()
    {
        return $this->createMock(Data::class);
    }

    /**
     * @return Context&MockObject
     */
    protected function getContextStub()
    {
        return $this->createMock(Context::class);
    }
}
