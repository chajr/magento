<?php

class Chajr_FreshSales_Helper_Account
{
    const FRESH_SALES_BASE_URL = '.freshsales.io/';
    const FRESH_SALES_PAGE = 'api/';
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
     * @return int
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \OutOfRangeException
     * @throws \DomainException
     */
    public function linkCustomerToFreshSales(Varien_Event_Observer $observer)
    {
        $this->loadCurlLib()
            ->getCustomerData($observer)
            ->createFreshSalesCustomer()
            ->setUserFreshSalesId();

        return $this->freshSalesCustomerId;
    }

    /**
     * @return $this
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \OutOfRangeException
     * @throws \DomainException
     */
    protected function createFreshSalesCustomer()
    {
        $content = json_encode([
            'lead' => $this->customerData
        ]);

        $uri = $this->baseApiUrl() . 'leads';

        $response = $this->curl->post(
            $uri,
            $content,
            $this->prepareRequestHeaders()
        );

        $this->handleFreshSalesErrors($response, $uri . ': ' . $content);

        $leadData = json_decode($response['response'], true);
        $this->freshSalesCustomerId = $leadData['lead']['id'];

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

    /**
     * @param array $response
     * @param string $request
     * @return $this
     * @throws \RuntimeException
     * @throws \OutOfRangeException
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws \DomainException
     *
     * @todo need to be refactored because of to big complexity
     */
    protected function handleFreshSalesErrors(array $response, $request)
    {
        $decoded = json_decode($response['response'], true);

        if (is_null($decoded)) {
            throw new \DomainException(
                'Undefined server response. Http code: '
                . $response['code']
                . '; Message: '
                . $response['response']
                . '; Error: '
                . $response['error']
                . '; Request: '
                . $request
            );
        }

        /**
         * Because FreshSales API return output that is not standardized
         * we need to check that each json has properly values.
         * Bellow examples of 500 http code sample outputs
         *
         * @example code: 500 output: {"error_code":500}
         * @example 500 output: {"errors":{"code":500,"message":["Last name can't be blank"]}}
         */
        switch ($response['code']) {
            case 401:
                if (isset($decoded['message'])) {
                    throw new \InvalidArgumentException(
                        'Authentication Failure: ' . $decoded['message'] . '; Request: ' . $request
                    );
                }
                $this->handleUndefinedResponse($decoded, $request);

            case 403:
                if (isset($decoded['errors']['message'])) {
                    throw new \InvalidArgumentException(
                        'Access Denied: ' . $decoded['errors']['message'] . '; Request: ' . $request
                    );
                }
                $this->handleUndefinedResponse($decoded, $request);

            case 404:
                throw new \UnexpectedValueException('Not found. Request: ' . $request);

            case 429:
                throw new \OutOfRangeException('Too many requests. Request: ' . $request);

            case 500:
                if (isset($decoded['errors']['message'][0])) {
                    throw new \RuntimeException(
                        'Unexpected Server Error: ' . $decoded['errors']['message'][0] . '; Request: ' . $request
                    );
                }
                $this->handleUndefinedResponse($decoded, $request);
        }

        return $this;
    }

    /**
     * @param array $decoded
     * @param string $request
     * @throws \UnexpectedValueException
     */
    protected function handleUndefinedResponse(array $decoded, $request)
    {
        throw new \UnexpectedValueException(
            'Undefined response format: ' . json_encode($decoded) . '; Request: ' . $request
        );
    }

    /**
     * @return $this
     */
    protected function setUserFreshSalesId()
    {
        $customer = Mage::getModel('customer/customer')->load($this->customerId);

        if ($customer->getId()) {
            $customer->setData('customer_freshsales_id', (string)$this->freshSalesCustomerId);

            $customer->save();
        }

        return $this;
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

        return self::FRESH_SALES_PROTOCOL . $login . self::FRESH_SALES_BASE_URL . self::FRESH_SALES_PAGE;
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
