<?php

class Chajr_FreshSales_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * @param Varien_Event_Observer $observer
     */
    public function customerRegisterSuccess(Varien_Event_Observer $observer)
    {
        $this->loadCurlLib();
        $curl = new Mage_HTTP_Client_Curl;

        $apiKey = Mage::getStoreConfig('chajr_freshsales/chajr_freshsales/chajr_freshsales_input');

        $curl->setHeaders([
            'Content-Type:application/json',
            'Accept:application/json',
            'Authorization: Token token=' . $apiKey,
        ]);
    }

    protected function loadCurlLib()
    {
        require_once Mage::getBaseDir('lib') . DS . 'Mage' . DS . 'HTTP' . DS . 'Client' . DS . 'Curl.php';
    }

    protected function getCustomerData(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();
        $customer = $event->getCustomer();

        return [
            
        ];
    }
}
