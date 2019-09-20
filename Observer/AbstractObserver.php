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
    /**
     * @param \Magento\Sales\Model\Order $order
     * @return null
     */
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
        $interactivNetwork = $this->configScopeConfigInterface->getValue(
            'interactivated/interactivated_customerreview/network',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

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
        if ($interactivNetwork == 'klantenvertellen') {
            $hash = $this->configScopeConfigInterface->getValue(
                'interactivated/interactivated_customerreview/hash',
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
            $location_id = $this->configScopeConfigInterface->getValue(
                'interactivated/interactivated_customerreview/location_id',
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
            $custom_delay_1 = $this->configScopeConfigInterface->getValue(
                'interactivated/interactivated_customerreview/custom_delay_1',
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
            $language_1 = $this->configScopeConfigInterface->getValue(
                'interactivated/interactivated_customerreview/language_1',
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
            $custom_servernew = $this->configScopeConfigInterface->getValue(
                'interactivated/interactivated_customerreview/custom_servernew',
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
            $invite_email = $email;
            $first_name = $order->getCustomerFirstname();
            $last_name = $order->getCustomerLastname();
            if (!$first_name){
                $first_name = $order->getShippingAddress()->getFirstname();
            }
            if (!$last_name){
                $last_name = $order->getShippingAddress()->getLastname();
            }
            $server = 'klantenvertellen.nl';
            if($custom_servernew=='newkiyoh.com'){
                $server = 'kiyoh.com';
            }

            $url = "https://{$server}/v1/invite/external?" .
                "hash={$hash}" .
                "&location_id={$location_id}" .
                "&invite_email={$invite_email}" .
                "&delay={$custom_delay_1}" .
                "&first_name={$first_name}" .
                "&last_name={$last_name}" .
                "&language={$language_1}";
        } else {
            $url = 'https://www.' . $interactivServer . '/set.php?user=' . $interactivUser .
                '&connector=' . $interactivConnector .
                '&action=' . $interactivAction .
                '&targetMail=' . $email .
                '&delay=' . $interactivDelay;

        }
        try {
            // create a new cURL resource
            $curl = curl_init();

            // set URL and other appropriate options
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_SSLVERSION, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
            curl_setopt($curl, CURLOPT_TIMEOUT, 2);
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
