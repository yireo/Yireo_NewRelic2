<?php
/**
 * NewRelic2 plugin for Magento
 *
 * @package     Yireo_NewRelic2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
 * @license     Simplified BSD License
 */

namespace Yireo\NewRelic2\Model;

abstract class Observer implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @param \Yireo\NewRelic2\Model\Service\Agent $agent
     * @param \Yireo\NewRelic2\Helper\Data $helper
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Indexer\IndexerInterface $indexer
     * @param \Magento\Store\Model\Store $store
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Registry $registry
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Yireo\NewRelic2\Model\Service\Agent $agent,
        \Yireo\NewRelic2\Helper\Data $helper,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Indexer\IndexerInterface $indexer,
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
     * Method to check wether this module can be used or not
     *
     * @return bool
     */
    protected function _isEnabled() 
    {
        return $this->helper->isEnabled();
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
