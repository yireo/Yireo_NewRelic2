<?php
/**
 * NewRelic2 plugin for Magento
 *
 * @package     Yireo_NewRelic2
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (http://www.yireo.com/)
 * @license     Simplified BSD License
 */

namespace Yireo\NewRelic2\Block\Rum\Timing;

class Footer extends Generic
{
    public function getContentHtml()
    {
        if (function_exists('newrelic_get_browser_timing_footer') == false) {
            return '';
        }

        $html = newrelic_get_browser_timing_footer(true);

        if($this->helper->isDebug()) {
            $html .= '<!-- newrelic_get_browser_timing_footer() -->';
        }

        return $html;
    }
}