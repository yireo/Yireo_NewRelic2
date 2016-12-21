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

class ControllerActionPostdispatchAdminhtmlProcessReindexProcess extends \Yireo\NewRelic2\Model\Observer
{
    /**
     * Listen to the event controller_action_postdispatch_adminhtml_process_reindexProcess
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isEnabled()) {
            return $this;
        }

        $processIds = (array)$this->request->getParam('process');
        if (!empty($processIds) && is_array($processIds)) {
            try {
                foreach ($processIds as $processId) {
                    $indexerCode = $this->getIndexerCodeByProcessId($processId);
                    $this->agent->addCustomMetric('Magento/Event/reindex:'.$indexerCode, (float)1.0);
                }

            } catch (\Exception $e) {
                $this->debug('Exception', $e->getMessage());
                return $this;
            }
        }

        return $this;
    }
}