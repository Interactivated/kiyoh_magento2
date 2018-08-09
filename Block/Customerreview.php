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
    protected $storeManagerInterface;

    /**
     * @var Registry
     */
    protected $frameworkRegistry;

    public function __construct(Context $context,
                                \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
                                \Magento\Framework\Registry $registry,
                                array $data = [])
    {
        parent::__construct($context, $data);
        $this->storeManagerInterface = $storeManagerInterface;
        $currentStore = $this->storeManagerInterface->getStore();
        $currentStoreId = $currentStore->getId();
        $this->cache = $context->getCache();

        $microdata = $this->_scopeConfig->getValue(
            'interactivated/interactivated_customerreview/show_microdata',
            ScopeInterface::SCOPE_STORE
        );
        if($microdata){
            $cache_key = 'interactivated_kiyoh_rating_' . $currentStoreId;

            $this->ratingString = $registry->registry($cache_key);
            if(!$this->ratingString){
                $this->ratingString = unserialize($this->cache->load($cache_key));
                if(!$this->ratingString){
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

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $file);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                    $output = curl_exec($ch);

                    if (curl_errno($ch)) {
                        $this->log('Kiyoh Curl error: ' . curl_error($ch));
                        $this->ratingString = $this->getPreviousValue($cache_key);
                    } else {
                        libxml_use_internal_errors(true);
                        $doc = simplexml_load_string($output);
                        if (!$doc || isset($doc->error)) {
                            $this->log(libxml_get_errors());
                            $this->log($doc->error);
                            $this->ratingString = $this->getPreviousValue($cache_key);
                        } else {
                            $this->ratingString = json_decode(json_encode($doc), TRUE);
                            $this->cache->save(serialize($this->ratingString),$cache_key,array(),3600);
                            $this->cache->save(serialize($this->ratingString),$cache_key . '_woexpire',array());
                        }
                    }
                    curl_close($ch);
                }
                $registry->unregister($cache_key);
                $registry->register($cache_key,$this->ratingString);
            }
        }

    }

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
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

        return unserialize($this->cache->load($cacheKey . '_woexpire'));
    }

    public function log ($data) {
        $this->_logger->debug(
            var_export($data, true),
            array(),
            true
        );
    }
}
