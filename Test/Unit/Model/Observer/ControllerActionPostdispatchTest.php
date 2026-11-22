<?php
declare(strict_types=1);

namespace Yireo\NewRelic2\Test\Unit\Model\Observer;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\Observer as CoreObserver;
use Magento\Framework\Indexer\IndexerInterface;
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

    public function testGuestFallbackIsUsedForEmptyCustomerData(): void
    {
        $this->helper->method('isEnabled')->willReturn(true);
        $this->createObserver('1', ' ', '')->execute(new CoreObserver());

        $this->assertSame('guest', $this->parameters['magento_customer_name']);
        $this->assertSame('guest', $this->parameters['magento_customer_email']);
    }

    public function testCustomerDataIsSent(): void
    {
        $this->helper->method('isEnabled')->willReturn(true);
        $this->createObserver('1', 'John Doe', 'john@example.com')->execute(new CoreObserver());

        $this->assertSame('John Doe', $this->parameters['magento_customer_name']);
        $this->assertSame('john@example.com', $this->parameters['magento_customer_email']);
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

    public function testUserAttributesAreSetWhenRumIsEnabled(): void
    {
        $this->helper->method('isEnabled')->willReturn(true);
        $this->helper->method('isUseRUM')->willReturn(true);
        $this->agent->expects($this->once())
            ->method('setUserAttributes')
            ->with('john@example.com', 'John Doe', null);

        $this->createObserver('1', 'John Doe', 'john@example.com')->execute(new CoreObserver());
    }

    public function testUserAttributesAreNotSetWhenRumIsDisabled(): void
    {
        $this->helper->method('isEnabled')->willReturn(true);
        $this->helper->method('isUseRUM')->willReturn(false);
        $this->agent->expects($this->never())->method('setUserAttributes');

        $this->createObserver('1')->execute(new CoreObserver());
    }

    private function createObserver(
        int|string $storeId,
        string $customerName = '',
        string $customerEmail = ''
    ): ControllerActionPostdispatch {
        $store = $this->createMock(StoreInterface::class);
        $store->method('getId')->willReturn($storeId);
        $storeManager = $this->createMock(StoreManagerInterface::class);
        $storeManager->method('getStore')->willReturn($store);

        $customer = new class ($customerName, $customerEmail) {
            public function __construct(
                private readonly string $name,
                private readonly string $email
            ) {
            }

            public function getName(): string
            {
                return $this->name;
            }

            public function getEmail(): string
            {
                return $this->email;
            }
        };
        $customerSession = $this->createMock(Session::class);
        $customerSession->method('getCustomer')->willReturn($customer);

        return new ControllerActionPostdispatch(
            $this->agent,
            $this->helper,
            $this->createMock(Http::class),
            $this->createMock(IndexerInterface::class),
            $storeManager,
            $customerSession,
            $this->registry,
            $this->createMock(LoggerInterface::class)
        );
    }
}
