<?php

namespace Interactivated\Customerreview\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractObserver
{
    /**
     * @var ScopeConfigInterface
     */
    protected $configScopeConfigInterface;

    /**
     * @var LoggerInterface
     */
    protected $logLoggerInterface;

    public function __construct(
        ScopeConfigInterface $configScopeConfigInterface,
        LoggerInterface $logLoggerInterface
    ) {
        $this->configScopeConfigInterface = $configScopeConfigInterface;
        $this->logLoggerInterface = $logLoggerInterface;
    }
    protected function _sendRequest($order)
    {
        $storeId = $order->getStoreId();
        $group_string = $this->configScopeConfigInterface->getValue(
            'interactivated/interactivated_customerreview/exclude_customer_groups',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $excludeCustomerGroups = array();
        if($group_string){
            $excludeCustomerGroups = explode(',',$group_string);
        }

        if (in_array($order->getCustomerGroupId(), $excludeCustomerGroups)) {
            return;
        }

        $email = $order->getCustomerEmail();
        $interactivServer = $this->configScopeConfigInterface->getValue(
            'interactivated/interactivated_customerreview/custom_server',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $interactivUser = $this->configScopeConfigInterface->getValue(
            'interactivated/interactivated_customerreview/custom_user',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $interactivConnector = $this->configScopeConfigInterface->getValue(
            'interactivated/interactivated_customerreview/custom_connector',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $interactivAction = $this->configScopeConfigInterface->getValue(
            'interactivated/interactivated_customerreview/custom_action',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $interactivDelay = $this->configScopeConfigInterface->getValue(
            'interactivated/interactivated_customerreview/custom_delay',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $url = 'https://www.'.$interactivServer.'/set.php?user='.$interactivUser.
            '&connector='.$interactivConnector.
            '&action='.$interactivAction.
            '&targetMail='.$email.
            '&delay='.$interactivDelay;

        try {
            // create a new cURL resource
            $curl = curl_init();

            // set URL and other appropriate options
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_SSLVERSION, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($curl, CURLOPT_TIMEOUT, 10);
            // grab URL and pass it to the browser
            $response = curl_exec($curl);
            if (curl_errno($curl)) {
                $this->logLoggerInterface->debug(
                    $response.'---Url---'.$url,
                    [],
                    true
                );
            }
        } catch (\Exception $e) {
            $this->logLoggerInterface->debug($e->getMessage(), [], true);
        }
        curl_close($curl);
    }
}
