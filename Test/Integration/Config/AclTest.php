<?php
declare(strict_types=1);

namespace Yireo\NewRelic2\Test\Integration\Config;

use Magento\Config\Model\Config\Structure;
use Magento\Framework\Acl\Builder;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea adminhtml
 */
class AclTest extends TestCase
{
    public function testConfigResourceIsDefined(): void
    {
        $acl = Bootstrap::getObjectManager()->get(Builder::class)->getAcl();

        $this->assertTrue($acl->hasResource('Yireo_NewRelic2::config'));
        $this->assertTrue($acl->inheritsResource('Yireo_NewRelic2::config', 'Magento_Config::config'));
    }

    public function testConfigSectionUsesResource(): void
    {
        $section = Bootstrap::getObjectManager()->get(Structure::class)->getElement('newrelic2');

        $this->assertSame('Yireo_NewRelic2::config', $section->getAttribute('resource'));
    }
}
