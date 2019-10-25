<?php
namespace Interactivated\Customerreview\Block;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

class Customerreview extends Template
{
    public $ratingString   = null;
    public $expirationTime = "+ 1 day";
    protected $configWriter;
    protected $cache;

    /**
     * @var Registry
     */
    protected $frameworkRegistry;

    public function __construct(Context $context,
                                \Magento\Framework\Registry $registry,
                                \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
                                array $data = [])
    {
        parent::__construct($context, $data);
        $this->configWriter = $configWriter;
        $currentStore = $this->_storeManager->getStore();
        $currentStoreId = $currentStore->getId();
        $this->cache = $context->getCache();

        $microdata = $this->_scopeConfig->getValue(
            'interactivated/interactivated_customerreview/show_microdata',
            ScopeInterface::SCOPE_STORE
        );
        $network = $this->_scopeConfig->getValue(
            'interactivated/interactivated_customerreview/network',
            ScopeInterface::SCOPE_STORE
        );
        if($microdata){
            $cache_key = 'interactivated_kiyoh_rating_' . $currentStoreId;

            $this->ratingString = $registry->registry($cache_key);
            if(!$this->ratingString){
                $this->ratingString = json_decode($this->cache->load($cache_key),true);
                if(!$this->ratingString){

                    $ch = curl_init();
                    if ($network=='klantenvertellen'){
                        $hash = $this->_scopeConfig->getValue(
                            'interactivated/interactivated_customerreview/hash',
                            ScopeInterface::SCOPE_STORE
                        );
                        $location_id = $this->_scopeConfig->getValue(
                            'interactivated/interactivated_customerreview/location_id',
                            ScopeInterface::SCOPE_STORE
                        );
                        $custom_servernew = $this->_scopeConfig->getValue(
                            'interactivated/interactivated_customerreview/custom_servernew',
                            ScopeInterface::SCOPE_STORE
                        );
                        $server = 'klantenvertellen.nl';
                        if ($custom_servernew=='newkiyoh.com'){
                            $server = 'kiyoh.com';
                        }
                        $url = "https://{$server}/v1/publication/review/external/location/statistics?locationId=" . $location_id;
                        $ch = curl_init();

                        // set url
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                            'X-Publication-Api-Token: ' . $hash
                        ));
                        //return the transfer as a string
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
                        // $output contains the output string
                        $output = curl_exec($ch);
                        try {
                            $rating = json_decode($output, true);
                            if ($rating && isset($rating['numberReviews'])){
                                $this->ratingString['company'] = array();
                                $this->ratingString['review_list'] = array();
                                $this->ratingString['company']['total_reviews'] = $rating['numberReviews'];
                                $this->ratingString['company']['total_score'] = $rating['averageRating'];
                                $this->ratingString['company']['url'] = $rating['viewReviewUrl'];
                                $this->cache->save(json_encode($this->ratingString),$cache_key,array(),3600);
                                $this->_saveToDb($cache_key, json_encode($this->ratingString));
                            } else {
                                $this->ratingString = $this->getPreviousValue($cache_key);
                                $this->_saveToDb($cache_key, json_encode($this->ratingString));
                            }
                        } catch(\Exception $e){
                            $this->ratingString = $this->getPreviousValue($cache_key);
                            $this->_saveToDb($cache_key, json_encode($this->ratingString));
                        }
                    } else {
                        $connector = $this->_scopeConfig->getValue(
                            'interactivated/interactivated_customerreview/custom_connector',
                            ScopeInterface::SCOPE_STORE
                        );
                        $company_id = $this->_scopeConfig->getValue(
                            'interactivated/interactivated_customerreview/company_id',
                            ScopeInterface::SCOPE_STORE
                        );
                        $custom_server = $this->_scopeConfig->getValue(
                            'interactivated/interactivated_customerreview/custom_server',
                            ScopeInterface::SCOPE_STORE
                        );

                        $file = 'https://'.$custom_server.'/xml/recent_company_reviews.xml?connectorcode='.$connector.'&company_id=' . $company_id;

                        curl_setopt($ch, CURLOPT_URL, $file);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
                        $output = curl_exec($ch);

                        if (curl_errno($ch)) {
                            $this->log('Kiyoh Curl error: ' . curl_error($ch));
                            $this->ratingString = $this->getPreviousValue($cache_key);
                        } else {
                            libxml_use_internal_errors(true);
                            $doc = simplexml_load_string($output);
                            if (!$doc) {
                                $this->log(libxml_get_errors());
                                $this->ratingString = $this->getPreviousValue($cache_key);
                                $this->_saveToDb($cache_key, json_encode($this->ratingString));
                            } elseif (isset($doc->error)) {
                                $this->log($doc->error);
                                $this->ratingString = $this->getPreviousValue($cache_key);
                                $this->_saveToDb($cache_key, json_encode($this->ratingString));
                            } else {
                                $this->ratingString = json_decode(json_encode($doc), true);
                                $this->cache->save(json_encode($this->ratingString),$cache_key,array(),3600);
                                $this->_saveToDb($cache_key, json_encode($this->ratingString));
                            }
                        }
                    }
                    curl_close($ch);
                }
                $registry->unregister($cache_key);
                $registry->register($cache_key,$this->ratingString);
            }
        }

    }

    public function getCustomerreview()
    {
        if (!$this->hasData('customerreview')) {
            $this->setData(
                'customerreview',
                $this->frameworkRegistry->registry('customerreview')
            );
        }
        return $this->getData('customerreview');
    }

    public function getReviews(){
        if(isset($this->ratingString['company']['total_reviews'])){
            return $this->ratingString['company']['total_reviews'];
        }
        return false;
    }

    public function getRating(){
        if(isset($this->ratingString['company']['total_score'])){
            return $this->ratingString['company']['total_score'];
        }
        return false;
    }
    public function getMicrodataUrl(){
        if(isset($this->ratingString['company']['url'])){
            return $this->ratingString['company']['url'];
        }
        return false;
    }
    public function getShowRating(){
        $show = $this->_scopeConfig->getValue(
            'interactivated/interactivated_customerreview/show_rating',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $show=='1';
    }
    public function getCorrectData(){
        return isset($this->ratingString['company']['total_reviews']);
    }
    public function getRatingPercentage(){
        if(isset($this->ratingString['company']['total_score'])){
            $val = floatval($this->ratingString['company']['total_score']);
            return ($val*10);
        }
        return false;
    }
    public function getMaxrating(){
        return 10;
    }

    public function getPreviousValue($cacheKey) {
        return json_decode($this->_scopeConfig->getValue('interactivated/interactivated_customerreview/kiyohresponse/' . $cacheKey),true);
    }

    public function log($data) {
        $this->_logger->debug(
            var_export($data, true),
            array(),
            true
        );
    }

    protected function _saveToDb($cacheKey, $value) {
        $this->configWriter->save('interactivated/interactivated_customerreview/kiyohresponse/' . $cacheKey, $value);
    }
}
