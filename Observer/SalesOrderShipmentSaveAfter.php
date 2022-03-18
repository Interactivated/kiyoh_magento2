<?php

namespace Interactivated\Customerreview\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\ScopeInterface;

class SalesOrderShipmentSaveAfter extends AbstractObserver implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $shipment = $observer->getEvent()->getShipment();
        $order = $shipment->getOrder();
        $storeId = $order->getStoreId();

        $interactivStatus = $this->configScopeConfigInterface->getValue(
            'interactivated/interactivated_customerreview/custom_enable',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $interactivEventval = $this->configScopeConfigInterface->getValue(
            'interactivated/interactivated_customerreview/custom_event',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if ($interactivEventval === 'Shipping' && $interactivStatus == '1') {
            $this->logger->info('salesOrderShipmentSaveAfter');
            $this->_sendRequest($order);
        }
    }
}
