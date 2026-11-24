<?php
declare(strict_types=1);

namespace Yireo\NewRelic2\Test\Integration\Config;

use Magento\Framework\Event\Config\Reader as EventReader;
use Magento\Framework\ObjectManager\Config\Reader\Dom as DiReader;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AreaScopeTest extends TestCase
{
    #[DataProvider('getObserverExpectations')]
    public function testObserversAreRegisteredInExpectedScopes(string $scope, string $event, string $name, bool $expected): void
    {
        $events = Bootstrap::getObjectManager()->create(EventReader::class)->read($scope);

        $this->assertSame($expected, isset($events[$event][$name]), "$name in $scope for $event");
    }

    public static function getObserverExpectations(): array
    {
        return [
            ['global', 'controller_action_postdispatch', 'yireo_newrelic2_controller_action_postdispatch', false],
            ['global', 'controller_action_predispatch', 'yireo_newrelic2_controller_action_predispatch', false],
            ['global', 'adminhtml_cache_flush_all', 'yireo_newrelic2_adminhtml_cache_flush_all', false],
            ['global', 'adminhtml_cache_flush_type', 'yireo_newrelic2_adminhtml_cache_flush_type', false],
            ['global', 'model_save_after', 'yireo_newrelic2_model_save_after', true],
            ['frontend', 'controller_action_postdispatch', 'yireo_newrelic2_controller_action_postdispatch', true],
            ['frontend', 'controller_action_predispatch', 'yireo_newrelic2_controller_action_predispatch', true],
            ['frontend', 'adminhtml_cache_flush_all', 'yireo_newrelic2_adminhtml_cache_flush_all', false],
            ['adminhtml', 'controller_action_postdispatch', 'yireo_newrelic2_controller_action_postdispatch', false],
            ['adminhtml', 'controller_action_predispatch', 'yireo_newrelic2_controller_action_predispatch', true],
            ['adminhtml', 'adminhtml_cache_flush_all', 'yireo_newrelic2_adminhtml_cache_flush_all', true],
            ['adminhtml', 'adminhtml_cache_flush_type', 'yireo_newrelic2_adminhtml_cache_flush_type', false],
        ];
    }

    #[DataProvider('getPluginExpectations')]
    public function testBlockPluginIsOnlyConfiguredForFrontend(string $scope, bool $expected): void
    {
        $config = Bootstrap::getObjectManager()->create(DiReader::class)->read($scope);
        $plugins = $config[AbstractBlock::class]['plugins'] ?? [];

        $this->assertSame($expected, isset($plugins['newrelic2-abstractblock']), "plugin in $scope");
    }

    public static function getPluginExpectations(): array
    {
        return [
            ['global', false],
            ['frontend', true],
            ['adminhtml', false],
        ];
    }
}
