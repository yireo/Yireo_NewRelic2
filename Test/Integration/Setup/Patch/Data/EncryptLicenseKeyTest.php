<?php
declare(strict_types=1);

namespace Yireo\NewRelic2\Test\Integration\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Yireo\NewRelic2\Setup\Patch\Data\EncryptLicenseKey;

/**
 * @magentoDbIsolation enabled
 */
class EncryptLicenseKeyTest extends TestCase
{
    public function testPlaintextLicenseIsEncrypted(): void
    {
        $this->saveLicense('plain-license');

        Bootstrap::getObjectManager()->create(EncryptLicenseKey::class)->apply();

        $value = $this->getLicense();
        $this->assertNotSame('plain-license', $value);
        $this->assertMatchesRegularExpression('/^\d+:\d+:/', $value);
        $this->assertSame(
            'plain-license',
            Bootstrap::getObjectManager()->get(EncryptorInterface::class)->decrypt($value)
        );
    }

    public function testEncryptedLicenseIsLeftAlone(): void
    {
        $encrypted = Bootstrap::getObjectManager()->get(EncryptorInterface::class)->encrypt('license');
        $this->saveLicense($encrypted);

        Bootstrap::getObjectManager()->create(EncryptLicenseKey::class)->apply();

        $this->assertSame($encrypted, $this->getLicense());
    }

    private function saveLicense(string $value): void
    {
        Bootstrap::getObjectManager()->get(WriterInterface::class)
            ->save(EncryptLicenseKey::CONFIG_PATH, $value);
    }

    private function getLicense(): string
    {
        $resource = Bootstrap::getObjectManager()->get(ResourceConnection::class);
        $connection = $resource->getConnection();
        $select = $connection->select()
            ->from($resource->getTableName('core_config_data'), ['value'])
            ->where('path = ?', EncryptLicenseKey::CONFIG_PATH)
            ->where('scope = ?', 'default');

        return (string)$connection->fetchOne($select);
    }
}
