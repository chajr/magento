<?php

class Chajr_FreshSales_Model_Observer extends Mage_Core_Model_Abstract
{
    public function customerRegisterSuccess(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();
        $customer = $event->getCustomer();
        $email = $customer->getEmail();
        Mage::log('Successfully logged in: ' . implode(',', [$customer, $email]), null, 'mylogfile.log');
    }
}
