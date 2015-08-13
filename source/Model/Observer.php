<?php
/**
 * NewRelic2 plugin for Magento
 *
 * @package     Yireo_NewRelic2
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (http://www.yireo.com/)
 * @license     Simplified BSD License
 */

namespace Yireo\NewRelic2\Model;

class Observer 
{
    /**
     * @param \Yireo\NewRelic2\Model\Service\Agent $agent
     * @param \Yireo\NewRelic2\Helper\Data $helper
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Indexer\Model\IndexerInterface $indexer
     * @param \Magento\Store\Model\Store $store
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Registry $registry
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Yireo\NewRelic2\Model\Service\Agent $agent,
        \Yireo\NewRelic2\Helper\Data $helper,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Indexer\Model\IndexerInterface $indexer,
        \Magento\Store\Model\Store $store,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Registry $registry,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->agent = $agent;
        $this->helper = $helper;
        $this->request = $request;
        $this->indexer = $indexer;
        $this->store = $store;
        $this->customerSession = $customerSession;
        $this->registry = $registry;
        $this->logger = $logger;
    }

    /**
     * Listen to the event adminhtml_cache_flush_all
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function adminhtmlCacheFlushAll(\Magento\Framework\Event\Observer $observer) 
    {
        if (!$this->_isEnabled()) {
            return $this;
        }
        
        $this->agent->addCustomMetric('Magento/Event/adminhtmlCacheFlushAll', (float)1.0);
        
        return $this;
    }

    /**
     * Listen to the event adminhtml_cache_flush_system
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function adminhtmlCacheFlushSystem(\Magento\Framework\Event\Observer $observer) 
    {
        if (!$this->_isEnabled()) {
            return $this;
        }

        $this->agent->addCustomMetric('Magento/Event/adminhtmlCacheFlushSystem', (float)1.0);
        
        return $this;
    }

    /**
     * Listen to the event adminhtml_cache_refresh_type
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function adminhtmlCacheRefreshType(\Magento\Framework\Event\Observer $observer) 
    {
        if (!$this->_isEnabled()) {
            return $this;
        }
        
        $event = $observer->getEvent();
        $refreshType = $event->getType();

        $this->agent->addCustomMetric('Magento/Event/adminhtmlCacheRefreshType:'.$refreshType, (float)1.0);
        
        return $this;
    }

    /**
     * Listen to the event controller_action_postdispatch_adminhtml_process_reindexProcess
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function controllerActionPostdispatchAdminhtmlProcessReindexProcess(\Magento\Framework\Event\Observer $observer) 
    {
        if (!$this->_isEnabled()) {
            return $this;
        }
        
        $processIds = (array)$this->request->getParam('process');
        if (!empty($processIds) && is_array($processIds)) {
            try {
                foreach ($processIds as $processId) {
                    $indexerCode = $this->getIndexerCodeByProcessId($processId);
                    $this->agent->addCustomMetric('Magento/Event/reindex:'.$indexerCode, (float)1.0);
                }

            } catch (\Exception $e) {
                $this->debug('Exception', $e->getMessage());
                return $this;
            }
        }

        return $this;
    }

    /**
     * Return the indexer code by process ID
     *
     * @param $processId
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getIndexerCodeByProcessId($processId)
    {
        $process = $this->indexer->getProcessById($processId);

        if (empty($process)) {
            throw new \InvalidArgumentException('Process ID is invalid: '.$processId);
        }


        $indexerCode = $process->getIndexerCode();
        return $indexerCode;
    }

    /**
     * Listen to the event controller_action_postdispatch_adminhtml_process_massReindex
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function controllerActionPostdispatchAdminhtmlProcessMassReindex(\Magento\Framework\Event\Observer $observer) 
    {
        if (!$this->_isEnabled()) {
            return $this;
        }

        $processIds = (array)$this->request->getParam('process');
        if (!empty($processIds) && is_array($processIds)) {
            try {
                foreach ($processIds as $processId) {
                    $indexerCode = $this->getIndexerCodeByProcessId($processId);
                    $this->agent->addCustomMetric('Magento/Event/reindex:'.$indexerCode, (float)1.0);
                }

            } catch (\Exception $e) {
                $this->debug('Exception', $e->getMessage());
                return $this;
            }
        }

        return $this;
    }

    /**
     * Listen to the event controller_action_predispatch
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function controllerActionPredispatch(\Magento\Framework\Event\Observer $observer) 
    {
        if (!$this->_isEnabled()) {
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

    /**
     * Method to setup the app-name
     *
     * @return $this
     */
    protected function _setupAppName() 
    {
        $helper = $this->helper;
        $appName = trim($helper->getAppName());
        $license = trim($helper->getLicense());
        $xmit = $helper->isUseXmit();

        $this->agent->setAppName($appName, $license, $xmit);

        return $this;
    }

    /**
     * Method to track the controller-action
     *
     * @param \Magento\Backend\Controller\Adminhtml\Cache\Index\Interceptor $action
     * @return $this
     */
    protected function _trackControllerAction($action) 
    {
        if (!$this->helper->isTrackController()) {
            return $this;
        }

        $request = $action->getRequest();
        $actionName = $request->getActionName();
        $this->agent->setNameTransaction($actionName);

        return $this;
    }

    /**
     * Post dispatch observer for user tracking
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function controllerActionPostdispatch(\Magento\Framework\Event\Observer $observer) 
    {
        if (!$this->_isEnabled()) {
            return $this;
        }

        // Set generic data
        $this->agent->addCustomParameter('magento_controller', $this->request->getControllerModule());
        $this->agent->addCustomParameter('magento_request', $this->request->getRequestUri());
        $this->agent->addCustomParameter('magento_store_id', $this->store->getId());

        // Get customer-data
        $customer = $this->customerSession->getCustomer();
        $customerName = trim($customer->getName());
        $customerEmail = trim($customer->getEmail());

        // Correct empty values
        if (empty($customerName)) {
            $customerName = 'guest';
        }

        if (empty($customerEmail)) {
            $customerEmail = 'guest';
        }

        // Set customer-data
        $this->agent->addCustomParameter('magento_customer_email', $customerEmail);
        $this->agent->addCustomParameter('magento_customer_name', $customerName);

        // Get and set product-data
        $product = $this->registry->registry('current_product');

        if (!empty($product)) {
            $productSku = $product->getSku();
            $this->agent->addCustomParameter('magento_product_name', $product->getName());
            $this->agent->addCustomParameter('magento_product_sku', $product->getSku());
            $this->agent->addCustomParameter('magento_product_id', $product->getId());
        } else {
            $productSku = null;
        }

        $category = $this->registry->registry('current_category');

        if ($category) {
            $this->agent->addCustomParameter('magento_category_name', $category->getName());
            $this->agent->addCustomParameter('magento_category_id', $category->getId());
        }

        // Set user attributes
        if ($this->helper->isUseRUM()) {
            $this->agent->setUserAttributes($customerEmail, $customerName, $productSku);
        }

        return $this;
    }

    /**
     * Listen to the event model_save_after
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function modelSaveAfter(\Magento\Framework\Event\Observer $observer) 
    {
        if ($this->_isEnabled()) {
            return $this;
        }

        $object = $observer->getEvent()->getObject();
        $this->agent->addCustomMetric(get_class($object) . '::save', (float)1.0);

        return $this;
    }

    /**
     * Listen to the event model_delete_after
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function modelDeleteAfter(\Magento\Framework\Event\Observer $observer) 
    {
        if (!$this->_isEnabled()) {
            return $this;
        }

        $object = $observer->getEvent()->getObject();
        $this->agent->addCustomMetric(get_class($object) . '::delete', (float)1.0);

        return $this;
    }

    /**
     * Method to check wether this module can be used or not
     *
     * @return bool
     */
    protected function _isEnabled() 
    {
        return $this->helper->isEnabled();
    }

    /**
     * Listen to the cron event always
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function crontab(\Magento\Framework\Event\Observer $observer) 
    {
        $this->agent->setBackgroundJob(true);
    }

    /**
     * @param string $string
     * @param mixed $variables
     */
    public function debug($string, $variables = null)
    {
        if ($this->helper->isDebug() == false) {
            return;
        }

        if (!empty($variables))
        {
            $string .= var_export($variables, true);
        }

        $this->logger->debug('NewRelic2 observer: '.$string);
    }
}
