<?php

class Chajr_FreshSales_Helper_Curl extends Mage_Core_Helper_Abstract
{
    protected $curl;

    public function __construct()
    {
        $this->curl = curl_init();
    }

    public function makeRequest($uri, $content, $headers, array $options = [])
    {
        
    }
}
