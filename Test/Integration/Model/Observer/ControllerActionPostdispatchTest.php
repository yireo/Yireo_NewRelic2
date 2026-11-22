<?php
declare(strict_types=1);

namespace Yireo\NewRelic2\Test\Integration\Model\Observer;

use Magento\Framework\Event\ConfigInterface as EventConfig;
use Magento\Framework\Event\Observer as CoreObserver;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Yireo\NewRelic2\Model\Observer\ControllerActionPostdispatch;
use Yireo\NewRelic2\Model\Service\Agent;

/**
 * @magentoAppArea frontend
 */
class ControllerActionPostdispatchTest extends TestCase
{
    public function testObserverIsRegisteredForPostdispatchEvent(): void
    {
        $eventConfig = Bootstrap::getObjectManager()->get(EventConfig::class);
        $observers = $eventConfig->getObservers('controller_action_postdispatch');

        $this->assertArrayHasKey('yireo_newrelic2_controller_action_postdispatch', $observers);
        $this->assertSame(
            ControllerActionPostdispatch::class,
            ltrim($observers['yireo_newrelic2_controller_action_postdispatch']['instance'], '\\')
        );
    }

    /**
     * @magentoConfigFixture current_store newrelic2/settings/enabled 1
     * @magentoConfigFixture current_store newrelic2/settings/fake_module 1
     */
    public function testCurrentStoreIdIsSentAsCustomParameter(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $parameters = [];

        $agent = $this->createMock(Agent::class);
        $agent->method('addCustomParameter')->willReturnCallback(
            function ($name, $value) use (&$parameters) {
                $parameters[$name] = $value;
                return true;
            }
        );

        $observer = $objectManager->create(ControllerActionPostdispatch::class, ['agent' => $agent]);
        $observer->execute(new CoreObserver());

        $currentStoreId = $objectManager->get(StoreManagerInterface::class)->getStore()->getId();
        $this->assertNotEmpty($currentStoreId);
        $this->assertArrayHasKey('magento_store_id', $parameters);
        $this->assertSame($currentStoreId, $parameters['magento_store_id']);
        $this->assertSame('guest', $parameters['magento_customer_email']);
    }
}
