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

/**
 * Class Footer
 *
 * @package Yireo\NewRelic2\Block\Rum\Timing
 */
class Footer extends Generic
{
    /**
     * @return string
     */
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