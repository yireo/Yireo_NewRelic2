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

class Header extends Generic
{
    public function getContentHtml()
    {
        if (function_exists('newrelic_get_browser_timing_header') == false) {
            return '';
        }

        $html = newrelic_get_browser_timing_header(true);

        if($this->helper->isDebug()) {
            $html .= '<!-- newrelic_get_browser_timing_header() -->';
        }

        return $html;
    }
}
