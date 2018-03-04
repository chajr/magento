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
     * @return $this
     */
    public function injectTabs($observer)
    {
        $block = $observer->getEvent()->getBlock();

        if ($block instanceof Mage_Adminhtml_Block_Customer_Edit_Tabs
            && ($this->getRequest()->getActionName() === 'edit' || $this->getRequest()->getParam('type'))
        ) {
            $customer = Mage::registry('current_customer');

            if (is_null($customer)) {
                return $this;
            }

            $customer->getCustomerFreshsalesId();

            $login = Mage::getStoreConfig('chajr_freshsales/chajr_freshsales/chajr_freshsales_input_login');

            $uri = Chajr_FreshSales_Helper_Account::FRESH_SALES_PROTOCOL
                . $login
                . Chajr_FreshSales_Helper_Account::FRESH_SALES_BASE_URL
                . 'leads/view/'
                . $customer->getCustomerFreshsalesId();

            $block->addTabAfter(
                'freshsales',
                [
                    'label' => Mage::helper('customer')->__('Freshsales Account'),
                    'url' => $uri,
                    'active' => false,
                    'class' => '',
                ],
                'account'
            );
        }

        return $this;
    }

    protected function getRequest()
    {
        return Mage::app()->getRequest();
    }
}
