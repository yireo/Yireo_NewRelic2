<?php
declare(strict_types=1);

namespace Yireo\NewRelic2\Test\Integration\Config;

use Magento\Config\Model\Config\Backend\Encrypted;
use Magento\Config\Model\Config\Structure;
use Magento\Config\Model\Config\TypePool;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea adminhtml
 */
class LicenseFieldTest extends TestCase
{
    public function testLicenseFieldIsEncryptedAndObscured(): void
    {
        $field = Bootstrap::getObjectManager()->get(Structure::class)
            ->getElement('newrelic2/settings/license');

        $this->assertSame('obscure', $field->getType());
        $this->assertInstanceOf(Encrypted::class, $field->getBackendModel());
    }

    public function testLicenseIsSensitive(): void
    {
        $typePool = Bootstrap::getObjectManager()->get(TypePool::class);

        $this->assertTrue($typePool->isPresent('newrelic2/settings/license', TypePool::TYPE_SENSITIVE));
    }
}
