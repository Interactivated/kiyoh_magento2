<?php

namespace Interactivated\Customerreview\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\ScopeInterface;

class SalesOrderPaymentAfter extends AbstractObserver implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $order = $observer->getOrder();
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

        if ($interactivEventval === 'Purchase' && $interactivStatus == '1') {
            $this->logLoggerInterface->debug(
                'salesOrderPaymentAfter',
                [],
                true
            );
            $this->_sendRequest($order);
        }
    }
}
