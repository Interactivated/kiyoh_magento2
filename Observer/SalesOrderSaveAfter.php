<?php

namespace Interactivated\Customerreview\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\ScopeInterface;

class SalesOrderSaveAfter extends AbstractObserver implements ObserverInterface
{
    public function execute(Observer $observer) {
        $order = $observer->getOrder();
        $storeId = $order->getStoreId();
        $interactivatedStatus = $this->configScopeConfigInterface->getValue(
            'interactivated/interactivated_customerreview/custom_enable',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $interactivatedEventval = $this->configScopeConfigInterface->getValue(
            'interactivated/interactivated_customerreview/custom_event',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $interactivatedOrderstatus = explode(
            ',',
            $this->configScopeConfigInterface->getValue(
                'interactivated/interactivated_customerreview/custom_event_order_status',
                ScopeInterface::SCOPE_STORE,
                $storeId
            )
        );

        if ($interactivatedEventval === 'Orderstatus' &&
            $interactivatedStatus == '1' &&
            in_array(
                $observer->getOrder()->getStatus(),
                $interactivatedOrderstatus
            )
        ) {
            $this->logger->info('salesOrderSaveAfter');
            $this->_sendRequest($observer->getOrder());
        }
    }
}
