<?php
/**
 * NewRelic2 plugin for Magento
 *
 * @package     Yireo_NewRelic2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
 * @license     Simplified BSD License
 */

namespace Yireo\NewRelic2\Model\Plugin;

/**
 * Class AbstractBlock
 *
 * @package Yireo\NewRelic2\Model\Plugin
 */
class AbstractBlock
{
    /**
     * @var \Yireo\NewRelic2\Model\Service\Agent
     */
    private $agent;

    /**
     * @var \Yireo\NewRelic2\Helper\Data
     */
    private $helper;

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

    /**
     * @param \Magento\Framework\View\Element\AbstractBlock $subject
     * @param $result
     *
     * @return mixed
     */
    public function afterToHtml(\Magento\Framework\View\Element\AbstractBlock $subject, $result)
    {
        if ($this->helper->isEnabled()) {
            $this->agent->addCustomParameter('block::'.$subject->getNameInLayout(), 1);
        }

        return $result;
    }
}
