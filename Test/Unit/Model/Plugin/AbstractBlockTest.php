<?php
declare(strict_types=1);

namespace Yireo\NewRelic2\Test\Unit\Model\Plugin;

use Magento\Framework\View\Element\AbstractBlock as CoreAbstractBlock;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yireo\NewRelic2\Helper\Data;
use Yireo\NewRelic2\Model\Plugin\AbstractBlock;
use Yireo\NewRelic2\Model\Service\Agent;

class AbstractBlockTest extends TestCase
{
    #[DataProvider('getSettings')]
    public function testBlockIsOnlyTrackedWhenEnabled(bool $enabled, bool $trackBlocks, bool $expected): void
    {
        $helper = $this->createMock(Data::class);
        $helper->method('isEnabled')->willReturn($enabled);
        $helper->method('isTrackBlocks')->willReturn($trackBlocks);

        $agent = $this->createMock(Agent::class);
        $agent->expects($expected ? $this->once() : $this->never())
            ->method('addCustomParameter')
            ->with('block::foo', 1);

        $block = $this->createMock(CoreAbstractBlock::class);
        $block->method('getNameInLayout')->willReturn('foo');

        $plugin = new AbstractBlock($agent, $helper);
        $this->assertSame('<p>html</p>', $plugin->afterToHtml($block, '<p>html</p>'));
    }

    public static function getSettings(): array
    {
        return [
            [true, true, true],
            [true, false, false],
            [false, true, false],
        ];
    }
}
