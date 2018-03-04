<?php

class Chajr_FreshSales_Test_Helper_Account extends EcomDev_PHPUnit_Test_Case
{
    protected function setUp()
    {
        parent::setUp();
    }

    public function testCreateAccount()
    {
        $curl = $this->getCurlLib([
            'code' => 200,
            'error' => '',
            'response' => '{"lead":{"id":3000344926}}',
        ]);

        $account = $this->createFullAccount();

        $this->setCurl($account, $curl);

        $this->assertEquals(3000344926, $account->linkCustomerToFreshSales($this->getObserver()));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage FreshSales API key is not defined.
     */
    public function testCreateAccountWithoutAPIKey()
    {
        $curl = $this->getCurlLib([
            'code' => 200,
            'error' => '',
            'response' => '{"lead":{"id":3000344926}}',
        ]);

        /** @var PHPUnit_Framework_MockObject_MockObject|Chajr_FreshSales_Helper_Account $mock */
        $account = $this->getMockBuilder(Chajr_FreshSales_Helper_Account::class)
            ->setMethods(['setUserFreshSalesId', 'loadCurlLib', 'baseApiUrl'])
            ->getMock();

        $account->method('setUserFreshSalesId')
            ->will($this->returnValue($account));

        $account->method('loadCurlLib')
            ->will($this->returnValue($account));

        $account->method('baseApiUrl')
            ->will($this->returnValue('domain.freshsales.io/api'));

        $this->setCurl($account, $curl);

        $account->linkCustomerToFreshSales($this->getObserver());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage FreshSales login is not defined.
     */
    public function testCreateAccountWithoutDomain()
    {
        $curl = $this->getCurlLib([
            'code' => 200,
            'error' => '',
            'response' => '{"lead":{"id":3000344926}}',
        ]);

        /** @var PHPUnit_Framework_MockObject_MockObject|Chajr_FreshSales_Helper_Account $mock */
        $account = $this->getMockBuilder(Chajr_FreshSales_Helper_Account::class)
            ->setMethods(['setUserFreshSalesId', 'loadCurlLib'])
            ->getMock();

        $account->method('setUserFreshSalesId')
            ->will($this->returnValue($account));

        $account->method('loadCurlLib')
            ->will($this->returnValue($account));

        $this->setCurl($account, $curl);

        $account->linkCustomerToFreshSales($this->getObserver());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCreateAccountWithAuthenticationFail()
    {
        $curl = $this->getCurlLib([
            'code' => 401,
            'error' => '',
            'response' => '{"login":"failed","message":"Incorrect or expired API key"}',
        ]);

        $account = $this->createFullAccount();

        $this->setCurl($account, $curl);

        $account->linkCustomerToFreshSales($this->getObserver());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCreateAccountWithAccessDenied()
    {
        $curl = $this->getCurlLib([
            'code' => 403,
            'error' => '',
            'response' => '{"errors":{"message":"Access Denied"}}',
        ]);

        $account = $this->createFullAccount();

        $this->setCurl($account, $curl);

        $account->linkCustomerToFreshSales($this->getObserver());
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testCreateAccountWithPageNotFound()
    {
        $curl = $this->getCurlLib([
            'code' => 404,
            'error' => '',
            'response' => '{"error_code":404}',
        ]);

        $account = $this->createFullAccount();

        $this->setCurl($account, $curl);

        $account->linkCustomerToFreshSales($this->getObserver());
    }

    /**
     * @expectedException OutOfRangeException
     */
    public function testCreateAccountWithToManyRequests()
    {
        $curl = $this->getCurlLib([
            'code' => 429,
            'error' => '',
            'response' => '{"error_code":429}',
        ]);

        $account = $this->createFullAccount();

        $this->setCurl($account, $curl);

        $account->linkCustomerToFreshSales($this->getObserver());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testCreateAccountWithServerError()
    {
        $curl = $this->getCurlLib([
            'code' => 500,
            'error' => '',
            'response' => '{"errors":{"code":500,"message":["Last name can\'t be blank"]}}',
        ]);

        $account = $this->createFullAccount();

        $this->setCurl($account, $curl);

        $account->linkCustomerToFreshSales($this->getObserver());
    }

    /**
     * @expectedException DomainException
     */
    public function testCreateAccountWithUndefinedError()
    {
        $curl = $this->getCurlLib([
            'code' => 500,
            'error' => '',
            'response' => '',
        ]);

        $account = $this->createFullAccount();

        $this->setCurl($account, $curl);

        $account->linkCustomerToFreshSales($this->getObserver());
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|Chajr_FreshSales_Helper_Account
     */
    protected function createFullAccount()
    {
        /** @var PHPUnit_Framework_MockObject_MockObject|Chajr_FreshSales_Helper_Account $mock */
        $account = $this->getMockBuilder(Chajr_FreshSales_Helper_Account::class)
            ->setMethods(['setUserFreshSalesId', 'loadCurlLib', 'baseApiUrl', 'prepareRequestHeaders'])
            ->getMock();

        $account->method('setUserFreshSalesId')
            ->will($this->returnValue($account));

        $account->method('loadCurlLib')
            ->will($this->returnValue($account));

        $account->method('baseApiUrl')
            ->will($this->returnValue('domain.freshsales.io/api/'));

        $account->method('prepareRequestHeaders')
            ->will($this->returnValue([
                'Content-Type:application/json',
                'Accept:application/json',
                'Authorization: Token token=eryseybstreytsrnyu',
            ]));

        return $account;
    }

    /**
     * @return Varien_Event_Observer
     */
    protected function getObserver()
    {
        $observer = new Varien_Event_Observer;
        $observer->setEvent(
            (new Varien_Object)->setCustomer(
                (new Varien_Object)->setData([
                    'entity_id' => 144,
                    'firstname' => 'test',
                    'lastname' => 'testing',
                    'email' => 'test@testme.com',
                    'created_at' => '2018-04-02T17:36:52Z',
                    'updated_at' => '2018-04-02T17:36:52Z',
                ])
            )
        );

        return $observer;
    }

    /**
     * @param PHPUnit_Framework_MockObject_MockObject|Chajr_FreshSales_Helper_Account $account
     * @param PHPUnit_Framework_MockObject_MockObject|Chajr_FreshSales_Helper_Curl $curl
     */
    protected function setCurl($account, $curl)
    {
        $refObject = new \ReflectionObject($account);
        $refProperty = $refObject->getProperty('curl');
        $refProperty->setAccessible(true);
        $refProperty->setValue($account, $curl);
    }

    /**
     * @param array $response
     * @return Chajr_FreshSales_Helper_Curl|PHPUnit_Framework_MockObject_MockObject
     */
    protected function getCurlLib(array $response)
    {
        /** @var PHPUnit_Framework_MockObject_MockObject|Chajr_FreshSales_Helper_Curl $mock */
        $mock = $this->getMockBuilder('Chajr_FreshSales_Helper_Curl')
            ->setMethods(['handleCurlResponse'])
            ->getMock();

        $mock->method('handleCurlResponse')
            ->will($this->returnValue($response));

        return $mock;
    }
}
