<?php

use CodesWholesaleFramework\Postback\ReceivePreOrders\EventDispatcher;

class CodesWholesale_ApiPlugin_Dispatchers_MageEventDispatcher implements EventDispatcher {

    function dispatchEvent(array $newKeys)
    {
        Mage::dispatchEvent('cw_send_pre_order_keys', $newKeys);
    }
}