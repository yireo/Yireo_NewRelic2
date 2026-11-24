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

use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\Observer as CoreObserver;
use Yireo\NewRelic2\Model\Observer;

/**
 * Class ControllerActionPostdispatch
 *
 * @package Yireo\NewRelic2\Model\Observer
 */
class ControllerActionPostdispatch extends Observer
{
    /**
     * Post dispatch observer for user tracking
     *
     * @param CoreObserver $observer
     *
     * @return $this
     */
    public function execute(CoreObserver $observer)
    {
        if (!$this->helper->isEnabled()) {
            return $this;
        }

        $this->agent->addCustomParameter('magento_controller', $this->request->getControllerModule());
        $this->agent->addCustomParameter('magento_request', $this->getRoute());
        $this->agent->addCustomParameter('magento_store_id', $this->storeManager->getStore()->getId());

        $customerId = $this->getCustomerId();
        if ($this->helper->isSendCustomerId()) {
            $this->agent->addCustomParameter('magento_customer_id', $customerId);
        }

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

        if ($this->helper->isUseRUM() && $this->helper->isSendCustomerId()) {
            $this->agent->setUserAttributes($customerId, '', (string)$productSku);
        }

        return $this;
    }

    /**
     * @return string
     */
    private function getRoute(): string
    {
        if ($this->request instanceof Http) {
            return (string)$this->request->getFullActionName('/');
        }

        return (string)$this->request->getModuleName() . '/' . (string)$this->request->getActionName();
    }

    /**
     * @return string
     */
    private function getCustomerId(): string
    {
        $customerId = (int)$this->customerSession->getCustomerId();

        return $customerId > 0 ? (string)$customerId : 'guest';
    }
}
