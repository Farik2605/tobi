<?php

class CodesWholesale_ApiPlugin_Statuses_MageSetPreOrderStatus{

    public function setStatus($orderId){

        Mage::helper('apiplugin/CwStatuses')->setPreOrderStatus($orderId);

    }
}