<?php

class Chajr_FreshSales_Model_Observer extends Mage_Core_Model_Abstract
{
    const FRESH_SALES_BASE_URL = '.freshsales.io/api/';
    const FRESH_SALES_PROTOCOL = 'https://';

    /**
     * @var Chajr_FreshSales_Helper_Curl
     */
    protected $curl;

    /**
     * @var array
     */
    protected $customerData = [];

    /**
     * @param Varien_Event_Observer $observer
     */
    public function customerRegisterSuccess(Varien_Event_Observer $observer)
    {
        try {
            $this->loadCurlLib()
                ->getCustomerData($observer)
                ->createFreshSalesCustomer()
                ->setUserFreshSalesId();
        } catch (\Exception $exception) {
            Mage::log('Creating FreshSales user exception:' . $exception->getMessage(), null, 'freshsales.log');
        }
    }

    /**
     * @return $this
     */
    protected function createFreshSalesCustomer()
    {
        $apiKey = Mage::getStoreConfig('chajr_freshsales/chajr_freshsales/chajr_freshsales_input_api_key');

        $content = json_encode([
            'lead' => $this->customerData
        ]);

        $uri = $this->baseApiUrl() . 'leads';

        $this->curl->setHeaders([
            'Content-Type:application/json',
            'Accept:application/json',
            'Authorization: Token token=' . $apiKey,
        ]);

        $this->curl->post($uri, $content);

        $response = [
            $this->curl->getStatus(),
            $this->curl->getBody(),
        ];

        return $this;
    }

    protected function setUserFreshSalesId()
    {
        
    }

    /**
     * @return $this
     */
    protected function loadCurlLib()
    {
        $this->curl = Mage::helper('chajr_freshsales/curl');;

        return $this;
    }

    /**
     * @return string
     */
    protected function baseApiUrl()
    {
        $login = Mage::getStoreConfig('chajr_freshsales/chajr_freshsales/chajr_freshsales_input_login');

        return self::FRESH_SALES_PROTOCOL . $login . self::FRESH_SALES_BASE_URL;
    }

    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    protected function getCustomerData(Varien_Event_Observer $observer)
    {
        /** @var Varien_Event $event */
        $event = $observer->getEvent();

        /** @var Mage_Customer_Model_Customer $customer */
        $customerData = $event->getCustomer()->getData();

        $this->customerData = [
            'id' => $customerData['entity_id'],
            'first_name' => $customerData['firstname'],
            'last_name' => $customerData['lastname'],
            'email' => $customerData['email'],
            'created_at' => $customerData['created_at'],
            'updated_at' => $customerData['updated_at'],
        ];

        return $this;
    }
}
