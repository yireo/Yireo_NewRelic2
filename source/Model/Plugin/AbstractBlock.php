<?php
/**
 * NewRelic2 plugin for Magento
 *
 * @package     Yireo_NewRelic2
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (http://www.yireo.com/)
 * @license     Simplified BSD License
 */

namespace Yireo\NewRelic2\Model\Plugin;

class AbstractBlock
{
    /**
     * @param \Yireo\NewRelic2\Model\Service\Agent $agent
     * @param \Yireo\NewRelic2\Helper\Data $helper
     */
    public function __construct(
        \Yireo\NewRelic2\Model\Service\Agent $agent,
        \Yireo\NewRelic2\Helper\Data $helper
    )
    {
        $this->agent = $agent;
        $this->helper = $helper;
    }

    public function afterToHtml(\Magento\Framework\View\Element\AbstractBlock $subject, $result)
    {
        if ($this->helper->isEnabled()) {
            $this->agent->addCustomParameter('block::'.$subject->getNameInLayout(), 1);
        }

        return $result;
    }
}
