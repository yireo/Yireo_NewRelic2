<?php
declare(strict_types=1);

namespace Yireo\NewRelic2\Test\Integration\Config;

use Magento\Framework\App\Area;
use Magento\Framework\App\AreaList;
use Magento\Framework\Config\ScopeInterface;
use Magento\Framework\Event\ConfigInterface as EventConfig;
use Magento\Framework\Interception\PluginListInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AreaScopeTest extends TestCase
{
    #[DataProvider('getObserverExpectations')]
    public function testObserversAreRegisteredInExpectedAreas(string $area, string $event, string $name, bool $expected): void
    {
        $this->setArea($area);
        $observers = Bootstrap::getObjectManager()->get(EventConfig::class)->getObservers($event);

        $this->assertSame($expected, array_key_exists($name, $observers), "$name in $area for $event");
    }

    public static function getObserverExpectations(): array
    {
        return [
            [Area::AREA_FRONTEND, 'controller_action_postdispatch', 'yireo_newrelic2_controller_action_postdispatch', true],
            [Area::AREA_ADMINHTML, 'controller_action_postdispatch', 'yireo_newrelic2_controller_action_postdispatch', false],
            [Area::AREA_FRONTEND, 'controller_action_predispatch', 'yireo_newrelic2_controller_action_predispatch', true],
            [Area::AREA_ADMINHTML, 'controller_action_predispatch', 'yireo_newrelic2_controller_action_predispatch', true],
            [Area::AREA_ADMINHTML, 'adminhtml_cache_flush_all', 'yireo_newrelic2_adminhtml_cache_flush_all', true],
            [Area::AREA_FRONTEND, 'adminhtml_cache_flush_all', 'yireo_newrelic2_adminhtml_cache_flush_all', false],
            [Area::AREA_ADMINHTML, 'adminhtml_cache_flush_type', 'yireo_newrelic2_adminhtml_cache_flush_type', false],
            [Area::AREA_ADMINHTML, 'model_save_after', 'yireo_newrelic2_model_save_after', true],
        ];
    }

    #[DataProvider('getPluginExpectations')]
    public function testBlockPluginIsOnlyActiveInFrontend(string $area, bool $expected): void
    {
        $this->setArea($area);
        $plugins = Bootstrap::getObjectManager()->get(PluginListInterface::class)
            ->getNext(AbstractBlock::class, 'toHtml');

        $names = [];
        array_walk_recursive($plugins, function ($value) use (&$names) {
            $names[] = $value;
        });

        $this->assertSame($expected, in_array('newrelic2-abstractblock', $names, true));
    }

    public static function getPluginExpectations(): array
    {
        return [
            [Area::AREA_FRONTEND, true],
            [Area::AREA_ADMINHTML, false],
        ];
    }

    private function setArea(string $area): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(ScopeInterface::class)->setCurrentScope($area);
        $objectManager->get(AreaList::class)->getArea($area)->load(Area::PART_CONFIG);
    }
}
