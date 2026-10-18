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

namespace Yireo\NewRelic2\Block\Rum\Timing;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Context;
use Yireo\NewRelic2\Helper\Data;

/**
 * Class Generic
 *
 * @package Yireo\NewRelic2\Block\Rum\Timing
 */
class Generic extends AbstractBlock
{
    /**
     * @var Data
     */
    protected Data $helper;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Data $helper
     * @param array $data
     */
    public function __construct(Context $context, Data $helper, array $data = [])
    {
        $this->helper = $helper;

        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getContentHtml()
    {
        return '';
    }

    /**
     * @return Data
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
