<?php

class CodesWholesale_ApiPlugin_Statuses_MageSetFailedStatus{

    public function setStatus($orderId, $totalNumberOfKeys){

        Mage::helper('apiplugin/CwStatuses')->setCompleteStatus($orderId, $totalNumberOfKeys);

    }
}