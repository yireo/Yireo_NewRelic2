<?php
/**
 * NewRelic2 plugin for Magento
 *
 * @package     Yireo_NewRelic2
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (http://www.yireo.com/)
 * @license     Simplified BSD License
 */

namespace Yireo\NewRelic2\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
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
     * Check whether the current area is the admin area
     *
     * @return bool
     */
    public function isAdmin()
    {
        $om         = \Magento\Framework\App\ObjectManager::getInstance();
        $appState  = $om->get('Magento\Framework\App\State');

        if($appState->getAreaCode() == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE)
        {
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
        $result = $this->scopeConfig->getValue(
            'newrelic2/settings/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (empty($result)) {
            $result = $defaultValue;
        }

        return $result;
    }

}
