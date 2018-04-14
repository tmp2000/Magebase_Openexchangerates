<?php
/**
 * @category    Magebase
 * @package     Magebase_Openexchangerates
 * @author      Robert Popovic
 * @copyright   Copyright (c) 2014 Magebase (http://magebase.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
/**
 * Currency rate import model (From www.webservicex.net)
 *
 * @category   Magebase
 * @package    Magebase_Openexchangerates
 * @author     Robert Popovic for Magebase
 */
class Magebase_Openexchangerates_Model_Currency_Import_Openexchangerates extends Mage_Directory_Model_Currency_Import_Abstract
{
    /**
     * Openexchangerates API URL
     * @var string
     */
    protected $_url = 'http://openexchangerates.org/api/latest.json?app_id={{APP_ID}}';
    protected $_messages = array();
 
     /**
     * HTTP client
     *
     * @var Varien_Http_Client
     */
    protected $_httpClient;
    /**
     * Fetched and cached rates array
     * @var array
     */
    protected $_rates;
 
    /**
     * Initialise our obkject with the full rates retrieved from Openexchangerates as the
     * free version only allows us to get the complete set of rates. But that's ok, we are
     * caching it and then processing the individual rates
     * 
     * @throws Exception
     */
    public function __construct()
    {
        $this->_httpClient = new Zend_Http_Client();
        $app_id = Mage::getStoreConfig('currency/mb_openexchangerates/app_id');
        if (!$app_id) {
            $e = new Exception(Mage::helper('mb_openexchangerates')->__('No Openexchangerates App Id set!'));
            Mage::logException($e);
            throw $e;
        }
        $url = str_replace('{{APP_ID}}', $app_id, $this->_url);
 
        $response = $this->_httpClient
            ->setUri($url)
            ->setConfig(array('timeout' => Mage::getStoreConfig('currency/mb_openexchangerates/timeout')))
            ->request('GET')
            ->getBody();
 
        // response is in json format
        if( !$response ) {
            $this->_messages[] = Mage::helper('mb_openexchangerates')->__('Cannot retrieve rate from %s.', $url);
        } else {
            // check response content - returns array
            $response = Zend_Json::decode($response);
            if (array_key_exists('error', $response)) {
                $this->_messages[] = Mage::helper('mb_openexchangerates')->__('API returned error %s: %s', $response['status'], $response['description']);
            } elseif (array_key_exists('base', $response) && array_key_exists('rates', $response)) {
                $this->_rates = $response['rates'];
 
            } else {
                Mage::log('Openexchangerates API request: %s',$url);
                Mage::log('Openexchangerates API response: '.print_r($response,true));
                $this->_messages[] = Mage::helper('mb_openexchangerates')->__('Unknown response from API, check system.log for details.');
            }
        }
    }
 
    /**
     * Convert an individual rate
     * Note that the Opexchangerates free service gives all rates based on USD
     * so we do a cross-currency conversion vua USD as the base.
     * 
     * @param string $currencyFrom
     * @param string $currencyTo
     * @param int $retry
     * @return float
     */
    protected function _convert($currencyFrom, $currencyTo, $retry=0)
    {
        if (sizeof($this->_messages) > 0) {
            return null;
        }
        $rate = null;
 
        if (array_key_exists($currencyFrom, $this->_rates) && array_key_exists($currencyTo, $this->_rates)) {
            // convert via base currency, whatever it is.
            $rate = (float) ($this->_rates[$currencyTo] * (1/$this->_rates[$currencyFrom]));
        } else {
            $this->_messages[] = Mage::helper('mb_openexchangerates')->__('Can\'t convert from '.$currencyFrom.' to '.$currencyTo.'. Rate doesn\'t exist.');
        }
 
        return $rate;
    }
    /**
     * Trim currency rate to 6 decimals
     * 
     * @param float $number
     * @return float
     */
    protected function _numberFormat($number)
    {
        return number_format($number, 6);
    }
}
