<?php
declare(strict_types=1);

namespace Yireo\NewRelic2\Test\Unit\Model\Observer;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\Observer as CoreObserver;
use Magento\Framework\Registry;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Yireo\NewRelic2\Helper\Data;
use Yireo\NewRelic2\Model\Observer\ControllerActionPostdispatch;
use Yireo\NewRelic2\Model\Service\Agent;

class ControllerActionPostdispatchTest extends TestCase
{
    private array $parameters = [];
    private Agent&MockObject $agent;
    private Data&MockObject $helper;
    private Registry&MockObject $registry;

    protected function setUp(): void
    {
        $this->parameters = [];
        $this->agent = $this->createMock(Agent::class);
        $this->agent->method('addCustomParameter')->willReturnCallback(
            function ($name, $value) {
                $this->parameters[$name] = $value;
                return true;
            }
        );

        $this->helper = $this->createMock(Data::class);
        $this->registry = $this->createMock(Registry::class);
    }

    public function testStoreIdOfCurrentStoreIsSentAsCustomParameter(): void
    {
        $this->helper->method('isEnabled')->willReturn(true);
        $this->createObserver('3')->execute(new CoreObserver());

        $this->assertSame('3', $this->parameters['magento_store_id']);
    }

    public function testAdminStoreIdIsSentAsCustomParameter(): void
    {
        $this->helper->method('isEnabled')->willReturn(true);
        $this->createObserver(0)->execute(new CoreObserver());

        $this->assertSame(0, $this->parameters['magento_store_id']);
    }

    public function testNothingIsSentWhenModuleIsDisabled(): void
    {
        $this->helper->method('isEnabled')->willReturn(false);
        $this->agent->expects($this->never())->method('addCustomParameter');
        $this->agent->expects($this->never())->method('setUserAttributes');

        $this->createObserver('1')->execute(new CoreObserver());
    }

    public function testRouteWithoutQueryStringIsSentAsRequest(): void
    {
        $this->helper->method('isEnabled')->willReturn(true);
        $this->createObserver('1')->execute(new CoreObserver());

        $this->assertSame('customer/account/createPassword', $this->parameters['magento_request']);
    }

    public function testCustomerEmailAndNameAreNeverSent(): void
    {
        $this->helper->method('isEnabled')->willReturn(true);
        $this->helper->method('isSendCustomerId')->willReturn(true);
        $this->createObserver('1', 42)->execute(new CoreObserver());

        $this->assertArrayNotHasKey('magento_customer_email', $this->parameters);
        $this->assertArrayNotHasKey('magento_customer_name', $this->parameters);
    }

    public function testCustomerIdIsNotSentByDefault(): void
    {
        $this->helper->method('isEnabled')->willReturn(true);
        $this->helper->method('isSendCustomerId')->willReturn(false);
        $this->createObserver('1', 42)->execute(new CoreObserver());

        $this->assertArrayNotHasKey('magento_customer_id', $this->parameters);
    }

    public function testCustomerIdIsSentWhenEnabled(): void
    {
        $this->helper->method('isEnabled')->willReturn(true);
        $this->helper->method('isSendCustomerId')->willReturn(true);
        $this->createObserver('1', 42)->execute(new CoreObserver());

        $this->assertSame('42', $this->parameters['magento_customer_id']);
    }

    public function testGuestIsSentAsCustomerIdForGuests(): void
    {
        $this->helper->method('isEnabled')->willReturn(true);
        $this->helper->method('isSendCustomerId')->willReturn(true);
        $this->createObserver('1')->execute(new CoreObserver());

        $this->assertSame('guest', $this->parameters['magento_customer_id']);
    }

    public function testProductAndCategoryDataIsSent(): void
    {
        $this->helper->method('isEnabled')->willReturn(true);

        $product = $this->createMock(Product::class);
        $product->method('getName')->willReturn('Foo');
        $product->method('getSku')->willReturn('foo-sku');
        $product->method('getId')->willReturn(42);

        $category = $this->createMock(Category::class);
        $category->method('getName')->willReturn('Bar');
        $category->method('getId')->willReturn(7);

        $this->registry->method('registry')->willReturnMap([
            ['current_product', $product],
            ['current_category', $category],
        ]);

        $this->createObserver('1')->execute(new CoreObserver());

        $this->assertSame('Foo', $this->parameters['magento_product_name']);
        $this->assertSame('foo-sku', $this->parameters['magento_product_sku']);
        $this->assertSame(42, $this->parameters['magento_product_id']);
        $this->assertSame('Bar', $this->parameters['magento_category_name']);
        $this->assertSame(7, $this->parameters['magento_category_id']);
    }

    public function testNoProductOrCategoryDataIsSentWithoutRegistryEntries(): void
    {
        $this->helper->method('isEnabled')->willReturn(true);
        $this->createObserver('1')->execute(new CoreObserver());

        $this->assertArrayNotHasKey('magento_product_id', $this->parameters);
        $this->assertArrayNotHasKey('magento_category_id', $this->parameters);
    }

    public function testUserAttributesContainOnlyCustomerIdWhenRumAndCustomerIdAreEnabled(): void
    {
        $this->helper->method('isEnabled')->willReturn(true);
        $this->helper->method('isUseRUM')->willReturn(true);
        $this->helper->method('isSendCustomerId')->willReturn(true);
        $this->agent->expects($this->once())
            ->method('setUserAttributes')
            ->with('42', '', '');

        $this->createObserver('1', 42)->execute(new CoreObserver());
    }

    public function testUserAttributesAreNotSetWithoutCustomerIdOption(): void
    {
        $this->helper->method('isEnabled')->willReturn(true);
        $this->helper->method('isUseRUM')->willReturn(true);
        $this->helper->method('isSendCustomerId')->willReturn(false);
        $this->agent->expects($this->never())->method('setUserAttributes');

        $this->createObserver('1', 42)->execute(new CoreObserver());
    }

    public function testUserAttributesAreNotSetWhenRumIsDisabled(): void
    {
        $this->helper->method('isEnabled')->willReturn(true);
        $this->helper->method('isUseRUM')->willReturn(false);
        $this->helper->method('isSendCustomerId')->willReturn(true);
        $this->agent->expects($this->never())->method('setUserAttributes');

        $this->createObserver('1')->execute(new CoreObserver());
    }

    private function createObserver(int|string $storeId, ?int $customerId = null): ControllerActionPostdispatch
    {
        $store = $this->createMock(StoreInterface::class);
        $store->method('getId')->willReturn($storeId);
        $storeManager = $this->createMock(StoreManagerInterface::class);
        $storeManager->method('getStore')->willReturn($store);

        $customerSession = $this->createMock(Session::class);
        $customerSession->method('getCustomerId')->willReturn($customerId);
        $customerSession->expects($this->never())->method('getCustomer');

        $request = $this->createMock(Http::class);
        $request->method('getFullActionName')->with('/')->willReturn('customer/account/createPassword');
        $request->method('getRequestUri')->willReturn('/customer/account/createPassword/?token=secret');

        return new ControllerActionPostdispatch(
            $this->agent,
            $this->helper,
            $request,
            $storeManager,
            $customerSession,
            $this->registry,
            $this->createMock(LoggerInterface::class)
        );
    }
}
