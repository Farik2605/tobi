<?php

use CodesWholesaleFramework\Postback\ReceivePreOrders\EventDispatcher;

class CodesWholesale_ApiPlugin_Dispatchers_MageOrderEventDispatcher implements EventDispatcher {

    function dispatchEvent(array $newKeys)
    {
        Mage::dispatchEvent('cw_send_keys_after_buy', $newKeys);
    }
}