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

namespace Yireo\NewRelic2\Model\Plugin;
use Magento\Framework\View\Element\AbstractBlock as CoreAbstractBlock;
use Yireo\NewRelic2\Helper\Data;
use Yireo\NewRelic2\Model\Service\Agent;

/**
 * Class AbstractBlock
 *
 * @package Yireo\NewRelic2\Model\Plugin
 */
class AbstractBlock
{
    /**
     * @var Agent
     */
    private $agent;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @param Agent $agent
     * @param Data $helper
     */
    public function __construct(
        Agent $agent,
        Data $helper
    )
    {
        $this->agent = $agent;
        $this->helper = $helper;
    }

    /**
     * @param CoreAbstractBlock $subject
     * @param $result
     *
     * @return mixed
     */
    public function afterToHtml(CoreAbstractBlock $subject, $result)
    {
        if ($this->helper->isEnabled()) {
            $this->agent->addCustomParameter('block::'.$subject->getNameInLayout(), 1);
        }

        return $result;
    }
}
