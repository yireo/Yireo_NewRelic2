<?php
/**
 * NewRelic2 plugin for Magento
 *
 * @package     Yireo_NewRelic2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
 * @license     Simplified BSD License
 */

declare(strict_types=1);

namespace Yireo\NewRelic2\Model\Observer;

use Magento\Framework\Event\ObserverInterface;
use Yireo\NewRelic2\Helper\Data;
use Yireo\NewRelic2\Model\Service\Agent;

/**
 * Class ModelSaveAfter
 *
 * @package Yireo\NewRelic2\Model\Observer
 */
class ModelSaveAfter implements ObserverInterface
{
    /**
     * @var Agent
     */
    protected $agent;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param Agent $agent
     * @param Data $helper
     */
    public function __construct(
        Agent $agent,
        Data $helper
    )
    {
        $this->agent = $agent;
        $this->helper = $helper;
    }

    /**
     * Listen to the event model_save_after
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isEnabled()) {
            return $this;
        }

        $object = $observer->getEvent()->getObject();
        $this->agent->addCustomMetric(get_class($object) . '::save', (float)1.0);

        return $this;
    }
}
