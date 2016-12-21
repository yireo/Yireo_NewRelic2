<?php
/**
 * NewRelic2 plugin for Magento
 *
 * @package     Yireo_NewRelic2
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (http://www.yireo.com/)
 * @license     Simplified BSD License
 */

namespace Yireo\NewRelic2\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Yireo\NewRelic2\Model\Observer
     */
    protected $targetModel;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * Setup method
     */
    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->targetModel = $objectManager->get('\Yireo\NewRelic2\Model\Observer');
    }

    /**
     * @test
     * @covers \Yireo\NewRelic2\Model\Observer::testGetIndexerCodeProcessById
     * @expectedException \InvalidArgumentException
     */
    public function testGetIndexerCodeProcessById()
    {
        $this->assertNotEmpty($this->targetModel->getIndexerCodeByProcessId(1));
    }
}