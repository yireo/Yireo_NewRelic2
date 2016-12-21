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

class ControllerActionPostdispatch extends \Yireo\NewRelic2\Model\Observer
{
    /**
     * Post dispatch observer for user tracking
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isEnabled()) {
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
}