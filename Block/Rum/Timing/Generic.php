<?php
/**
 * NewRelic2 plugin for Magento
 *
 * @package     Yireo_NewRelic2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
 * @license     Simplified BSD License
 */

namespace Yireo\NewRelic2\Block\Rum\Timing;

/**
 * Class Generic
 *
 * @package Yireo\NewRelic2\Block\Rum\Timing
 */
class Generic extends \Magento\Framework\View\Element\AbstractBlock
{
    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param array $data
     * @param \Yireo\NewRelic2\Helper\Data
     */
    public function __construct(\Magento\Framework\View\Element\Context $context, array $data = [], \Yireo\NewRelic2\Helper\Data $helper)
    {
        $rt = parent::__construct($context, $data);

        $this->helper = $helper;

        return $rt;
    }

    /**
     * @return mixed
     */
    public function getContentHtml()
    {
    }

    /**
     * @return \Yireo\NewRelic2\Helper\Data
     */
    protected function _getHelper()
    {
        return $this->helper;
    }

    /**
     * @return bool
     */
    protected function _canShow()
    {
        $isEnabled = $this->_getHelper()->isEnabled();
        $isUseRum = $this->_getHelper()->isUseRUM();

        return $isEnabled && $isUseRum;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->_canShow() == false) {
            return '';
        }

        return $this->getContentHtml();
    }
}
