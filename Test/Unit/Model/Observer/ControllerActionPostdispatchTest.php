<?php
declare(strict_types=1);

namespace Yireo\NewRelic2\Test\Unit\Model\Observer;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\Observer as CoreObserver;
use Magento\Framework\Indexer\IndexerInterface;
use Magento\Framework\Registry;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Yireo\NewRelic2\Helper\Data;
use Yireo\NewRelic2\Model\Observer\ControllerActionPostdispatch;
use Yireo\NewRelic2\Model\Service\Agent;

class ControllerActionPostdispatchTest extends TestCase
{
    public function testStoreIdOfCurrentStoreIsSentAsCustomParameter(): void
    {
        $parameters = [];

        $agent = $this->createMock(Agent::class);
        $agent->method('addCustomParameter')->willReturnCallback(
            function ($name, $value) use (&$parameters) {
                $parameters[$name] = $value;
                return true;
            }
        );

        $helper = $this->createMock(Data::class);
        $helper->method('isEnabled')->willReturn(true);
        $helper->method('isUseRUM')->willReturn(false);

        $request = $this->createMock(Http::class);

        $store = $this->createMock(StoreInterface::class);
        $store->method('getId')->willReturn('3');
        $storeManager = $this->createMock(StoreManagerInterface::class);
        $storeManager->method('getStore')->willReturn($store);

        $customer = new class {
            public function getName(): string
            {
                return '';
            }

            public function getEmail(): string
            {
                return '';
            }
        };
        $customerSession = $this->createMock(Session::class);
        $customerSession->method('getCustomer')->willReturn($customer);

        $observer = new ControllerActionPostdispatch(
            $agent,
            $helper,
            $request,
            $this->createMock(IndexerInterface::class),
            $storeManager,
            $customerSession,
            $this->createMock(Registry::class),
            $this->createMock(LoggerInterface::class)
        );

        $observer->execute(new CoreObserver());

        $this->assertSame('3', $parameters['magento_store_id']);
    }
}
