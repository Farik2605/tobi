<?php

class CodesWholesale_ApiPlugin_Statuses_MageSetCompleteStatus{

    public function setStatus($order, $totalNumberOfKeys){

        Mage::helper('apiplugin/CwStatuses')->setCompleteStatus($order->getId(), $totalNumberOfKeys);
    }
}