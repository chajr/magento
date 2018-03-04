<?php

class Chajr_FreshSales_Test_Helper_Curl extends EcomDev_PHPUnit_Test_Case
{
    protected function setUp()
    {
        parent::setUp();
    }

    public function testCurlExecution()
    {
        /** @var PHPUnit_Framework_MockObject_MockObject|Chajr_FreshSales_Helper_Curl $mock */
        $mock = $this->getMockBuilder(Chajr_FreshSales_Helper_Curl::class)
            ->setMethods(['handleCurlResponse'])
            ->getMock();

        $mock->method('handleCurlResponse')
            ->will($this->returnValue($this->sampleData()));

        $this->assertEquals(
            $this->sampleData(),
            $mock->post('someUrl.com', 'some data')
        );

        $this->assertEquals(
            $this->sampleData(),
            $mock->makeRequest(
                'someUrl.com',
                'some data',
                [],
                ['post' => 1]
            )
        );
    }

    protected function sampleData()
    {
        return [
            'code' => 200,
            'error' => '',
            'response' => '{"lead":{"id":3000344926}}',
        ];
    }
}
