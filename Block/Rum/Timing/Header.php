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
 * Class Header
 *
 * @package Yireo\NewRelic2\Block\Rum\Timing
 */
class Header extends Generic
{
    public function getContentHtml()
    {
        if (function_exists('newrelic_get_browser_timing_header') == false) {
            return '';
        }

        return $this->renderScript((string)newrelic_get_browser_timing_header(false));
    }
}
