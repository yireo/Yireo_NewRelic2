<?php
/**
 * NewRelic2 plugin for Magento
 *
 * @package     Yireo_NewRelic2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
 * @license     Simplified BSD License
 */

namespace Yireo\NewRelic2\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\State $appState
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\State $appState
    )
    {
        $this->appState = $appState;

        parent::__construct($context);
    }

    /**
     * Check whether this module can be used
     *
     * @return bool
     */
    public function isEnabled()
    {
        if (!extension_loaded('newrelic')) {
            return false;
        }

        return $this->getConfigFlag('enabled');
    }

    /**
     * Check whether debugging is enabled
     *
     * @return bool
     */
    public function isDebug()
    {
        if ($this->isEnabled() == false) {
            return false;

        }

        return $this->getConfigFlag('debug');
    }

    /**
     * Check whether the current area is the admin area
     *
     * @return bool
     */
    public function isAdmin()
    {
        if ($this->appState->getAreaCode() == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {
            return true;
        }

        return false;
    }

    /**
     * Return the appname
     *
     * @return string
     */
    public function getAppName()
    {
        return $this->getConfigValue('appname');
    }

    /**
     * Return the New Relic license
     *
     * @return string
     */
    public function getLicense()
    {
        return $this->getConfigValue('license');
    }

    /**
     * Return whether to use the xmit flag
     *
     * @return bool
     */
    public function isUseXmit()
    {
        return $this->getConfigFlag('xmit');
    }

    /**
     * Return whether to track the controller
     *
     * @return bool
     */
    public function isTrackController()
    {
        return $this->getConfigFlag('track_controller');
    }

    /**
     * Return whether to use Real User Monitoring
     *
     * @return bool
     */
    public function isUseRUM()
    {
        return $this->getConfigFlag('real_user_monitoring');
    }

    /**
     * Return a value from the configuration
     *
     * @param string $key
     * @param mixed $defaultValue
     *
     * @return bool
     */
    public function getConfigValue($key = null, $defaultValue = null)
    {
        $value = $this->scopeConfig->getValue(
            'newrelic2/settings/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (empty($value)) {
            $value = $defaultValue;
        }

        return $value;
    }

    /**
     * Return a boolean flag for the configuration
     *
     * @param string $key
     * @param mixed $defaultValue
     *
     * @return bool
     */
    public function getConfigFlag($key = null, $defaultValue = false)
    {
        $value = $this->scopeConfig->getValue(
            'newrelic2/settings/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (empty($value)) {
            $value = $defaultValue;
        }

        return (bool)$value;
    }
}
