<?php
declare(strict_types=1);

namespace Yireo\NewRelic2\Test\Unit\Model\Observer;

use Magento\Framework\DataObject;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer as CoreObserver;
use PHPUnit\Framework\TestCase;
use Yireo\NewRelic2\Helper\Data;
use Yireo\NewRelic2\Model\Observer\ModelDeleteAfter;
use Yireo\NewRelic2\Model\Observer\ModelSaveAfter;
use Yireo\NewRelic2\Model\Service\Agent;

class ModelSaveAfterTest extends TestCase
{
    public function testNoMetricIsAddedWhenModelTrackingIsDisabled(): void
    {
        $agent = $this->createMock(Agent::class);
        $agent->expects($this->never())->method('addCustomMetric');

        (new ModelSaveAfter($agent, $this->getHelper(false)))->execute($this->getObserver());
        (new ModelDeleteAfter($agent, $this->getHelper(false)))->execute($this->getObserver());
    }

    public function testMetricIsAddedWhenModelTrackingIsEnabled(): void
    {
        $agent = $this->createMock(Agent::class);
        $agent->expects($this->once())
            ->method('addCustomMetric')
            ->with(DataObject::class . '::save', 1.0);

        (new ModelSaveAfter($agent, $this->getHelper(true)))->execute($this->getObserver());
    }

    private function getHelper(bool $trackModels): Data
    {
        $helper = $this->createMock(Data::class);
        $helper->method('isEnabled')->willReturn(true);
        $helper->method('isTrackModels')->willReturn($trackModels);

        return $helper;
    }

    private function getObserver(): CoreObserver
    {
        return new CoreObserver(['event' => new Event(['object' => new DataObject()])]);
    }
}
