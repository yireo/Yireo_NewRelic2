<?php
/**
 * NewRelic2 plugin for Magento
 *
 * @package     Yireo_NewRelic2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
 * @license     Simplified BSD License
 */

namespace Yireo\NewRelic2\Model\Service;

/**
 * Class Agent
 *
 * @package Yireo\NewRelic2\Model\Service
 */
class Agent
{
    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->logger = $logger;
    }

    /**
     * @param $name
     * @param $value
     *
     * @return bool
     */
    public function addCustomMetric($name, $value)
    {
        $this->debug('Calling addCustomMetric', array($name, $value));

        if ($this->functionExists('newrelic_custom_metric') == false) {
            return false;
        }

        newrelic_custom_metric($name, $value);

        return true;
    }

    /**
     * @param $name
     * @param $value
     *
     * @return bool
     */
    public function addCustomParameter($name, $value)
    {
        $this->debug('Calling addCustomerParameter', array($name, $value));

        if ($this->functionExists('newrelic_custom_parameter') == false) {
            return false;
        }

        newrelic_custom_parameter($name, $value);

        return true;
    }

    /**
     *
     */
    public function setUserAttributes()
    {
        $parameters = func_get_args();
        $this->debug('Calling setUserAttributes', $parameters);

        if($this->functionExists('newrelic_set_user_attributes')) {
            call_user_func_array('newrelic_set_user_attributes', $parameters);
        }
    }

    /**
     * @param $backgroundJob
     */
    public function setBackgroundJob($backgroundJob)
    {
        $this->debug('Calling setBackgroundJob');

        if($this->functionExists('newrelic_background_job')) {
            newrelic_background_job($backgroundJob);
        }
    }

    /**
     * @param $name
     */
    public function setNameTransaction($name)
    {
        $this->debug('Calling setNameTransaction', array($name));

        if ($this->functionExists('newrelic_name_transaction')) {
            newrelic_name_transaction($name);
        }
    }

    /**
     * @param $name
     * @param $license
     * @param $xmit
     */
    public function setAppName($name, $license, $xmit)
    {
        $parameters = func_get_args();
        $this->debug('Calling setAppName', $parameters);

        if (!empty($name) && $this->functionExists('newrelic_set_appname')) {
            newrelic_set_appname($name, $license, $xmit);
        }
    }

    /**
     * @param string $function
     *
     * @return bool
     */
    public function functionExists($function)
    {
        return function_exists($function);
    }

    /**
     * @param string $string
     * @param mixed $variables
     */
    public function debug($string, $variables = null)
    {
        if (!empty($variables))
        {
            $string .= var_export($variables, true);
        }

        $this->logger->debug('NewRelic2 agent: '.$string);
    }
}
