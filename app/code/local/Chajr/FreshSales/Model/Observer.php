<?php

class Chajr_FreshSales_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * @param Varien_Event_Observer $observer
     */
    public function customerRegisterSuccess(Varien_Event_Observer $observer)
    {
        /** @var Chajr_FreshSales_Helper_Account $account */
        $account = Mage::helper('chajr_freshsales/account');

        $account->linkCustomerToFreshSales($observer);
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function injectTabs($observer)
    {
        /** @var Chajr_FreshSales_Helper_Admin $account */
        $account = Mage::helper('chajr_freshsales/admin');

        $account->addFreshSalesTab($observer);
    }
}
