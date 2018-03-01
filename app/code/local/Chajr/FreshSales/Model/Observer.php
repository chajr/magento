<?php

class Chajr_FreshSales_Model_Observer extends Mage_Core_Model_Abstract
{
    //@todo check that user already exists
    //@todo update customer account => set freshsales id

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
     * @var int
     */
    protected $customerId;

    /**
     * @var int
     */
    protected $freshSalesCustomerId;

    /**
     * @param Varien_Event_Observer $observer
     */
    public function customerRegisterSuccess(Varien_Event_Observer $observer)
    {
        try {
            $this->loadCurlLib()
                ->getCustomerData($observer)
                ->checkThatUserExists()
                ->createFreshSalesCustomer()
                ->setUserFreshSalesId();
        } catch (\Exception $exception) {
            Mage::log('Creating FreshSales user exception:' . $exception->getMessage(), null, 'freshsales.log');
        }
    }

    /**
     * @return $this
     */
    protected function checkThatUserExists()
    {
        $content = json_encode([
            'lead' => $this->customerData
        ]);

        $response = $this->curl->post(
            $this->baseApiUrl() . 'leads',
            $content,
            $this->prepareRequestHeaders()
        );

        $this->handleFreshSalesErrors($response);
//        throw new \InvalidArgumentException('Customer already exists: ');
        return $this;
    }

    /**
     * @return $this
     * @throws \InvalidArgumentException
     */
    protected function createFreshSalesCustomer()
    {
        $content = json_encode([
            'lead' => $this->customerData
        ]);

        $response = $this->curl->post(
            $this->baseApiUrl() . 'leads',
            $content,
            $this->prepareRequestHeaders()
        );

        $this->handleFreshSalesErrors($response);

        return $this;
    }

    /**
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function prepareRequestHeaders()
    {
        $apiKey = Mage::getStoreConfig('chajr_freshsales/chajr_freshsales/chajr_freshsales_input_api_key');

        if (!$apiKey) {
            throw new \InvalidArgumentException('FreshSales API key is not defined.');
        }

        return [
            'Content-Type:application/json',
            'Accept:application/json',
            'Authorization: Token token=' . $apiKey,
        ];
    }

    protected function handleFreshSalesErrors($response)
    {
        
    }

    protected function setUserFreshSalesId()
    {
        
    }

    /**
     * @return $this
     */
    protected function loadCurlLib()
    {
        $this->curl = Mage::helper('chajr_freshsales/curl');

        return $this;
    }

    /**
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function baseApiUrl()
    {
        $login = Mage::getStoreConfig('chajr_freshsales/chajr_freshsales/chajr_freshsales_input_login');

        if (!$login) {
            throw new \InvalidArgumentException('FreshSales login is not defined.');
        }

        return self::FRESH_SALES_PROTOCOL . $login . self::FRESH_SALES_BASE_URL;
    }

    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     * @throws \UnexpectedValueException
     */
    protected function getCustomerData(Varien_Event_Observer $observer)
    {
        /** @var Varien_Event $event */
        $event = $observer->getEvent();

        /** @var Mage_Customer_Model_Customer $customer */
        $customerData = $event->getCustomer()->getData();

        if (empty($customerData)) {
            throw new \UnexpectedValueException('Empty Magento user data.');
        }

        $this->customerId = $customerData['entity_id'];

        $this->customerData = [
            'first_name' => $customerData['firstname'],
            'last_name' => $customerData['lastname'],
            'email' => $customerData['email'],
            'created_at' => $customerData['created_at'],
            'updated_at' => $customerData['updated_at'],
        ];

        return $this;
    }
}
