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

use Magento\Framework\Event\Observer as CoreObserver;
use Magento\Framework\Exception\LocalizedException;
use Yireo\NewRelic2\Model\Observer;

/**
 * Class ControllerActionPredispatch
 *
 * @package Yireo\NewRelic2\Model\Observer
 */
class ControllerActionPredispatch extends Observer
{
    /**
     * Listen to the event controller_action_predispatch
     *
     * @param CoreObserver $observer
     *
     * @return $this
     * @throws LocalizedException
     */
    public function execute(CoreObserver $observer)
    {
        if (!$this->helper->isEnabled()) {
            return $this;
        }

        $this->_setupAppName();
        $this->_trackControllerAction($observer->getEvent()->getControllerAction());

        // Ignore Apdex for Magento Admin Panel pages
        if ($this->helper->isAdmin()) {
            if(function_exists('newrelic_ignore_apdex')) {
                newrelic_ignore_apdex();
            }
        }

        // Common settings
        if(function_exists('newrelic_capture_params')) {
            newrelic_capture_params(true);
        }

        return $this;
    }
}
