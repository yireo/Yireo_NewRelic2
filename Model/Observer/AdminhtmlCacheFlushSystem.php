<?php
/**
 * NewRelic2 plugin for Magento
 *
 * @package     Yireo_NewRelic2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
 * @license     Simplified BSD License
 */

namespace Yireo\NewRelic2\Model\Observer;

class AdminhtmlCacheFlushSystem implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Yireo\NewRelic2\Model\Service\Agent
     */
    protected $agent;

    /**
     * @var \Yireo\NewRelic2\Helper\Data
     */
    protected $helper;

    /**
     * @param \Yireo\NewRelic2\Model\Service\Agent $agent
     * @param \Yireo\NewRelic2\Helper\Data $helper
     */
    public function __construct(
        \Yireo\NewRelic2\Model\Service\Agent $agent,
        \Yireo\NewRelic2\Helper\Data $helper
    )
    {
        $this->agent = $agent;
        $this->helper = $helper;
    }

    /**
     * Listen to the event adminhtml_cache_flush_system
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isEnabled()) {
            return $this;
        }

        $this->agent->addCustomMetric('Magento/Event/adminhtmlCacheFlushSystem', (float)1.0);

        return $this;
    }
}