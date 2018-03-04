<?php

class Chajr_FreshSales_Test_Helper_Admin extends EcomDev_PHPUnit_Test_Case
{
    protected function setUp()
    {
        parent::setUp();
    }

    public function testAddTab()
    {
        $block = new Mage_Adminhtml_Block_Customer_Edit_Tabs;
        $observer = new Varien_Event_Observer;

        $observer->setEvent((new Varien_Object)->setBlock($block));

        /** @var PHPUnit_Framework_MockObject_MockObject|Chajr_FreshSales_Helper_Admin $mock */
        $mockAdmin = $this->getMockBuilder(Chajr_FreshSales_Helper_Admin::class)
            ->setMethods(['getCustomer', 'getLogin', 'getRequest'])
            ->getMock();

        $mockAdmin->method('getLogin')
            ->will($this->returnValue('domain'));

        $mockAdmin->method('getCustomer')
            ->will(
                $this->returnValue(
                    (new Varien_Object)->setCustomerFreshsalesId(1000)
                )
            );

        $mockAdmin->method('getRequest')
            ->will(
                $this->returnValue(
                    (new Varien_Object)->setActionName('edit')
                )
            );

        $mockAdmin->addFreshSalesTab($observer);

        $this->assertEquals(['freshsales'], $block->getTabsIds());
    }
}
