<?php

class CodesWholesale_ApiPlugin_Helper_CwStatuses extends Mage_Core_Helper_Abstract
{
    /**
     * Sets Status for Completed order by CodesWholesale
     */

    public function setCompleteStatus($orderId, $totalNumberOfKeys)
    {
        $order = Mage::getModel('sales/order')->load($orderId);
        $status = Mage::getModel('sales/order_status')->loadDefaultByState('codeswholesale_completed_state');
        $status = $status->getStatus();
        $comment = 'Key\'s are sent to ' . $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname() . '(total: ' . $totalNumberOfKeys . ')' ;
        $order->setStatus($status);
        $order->addStatusHistoryComment($comment, false);
        $order->save();
    }

    /*
     * Sets Status for PreOrdered Games in CodesWholesale
     */
    public function setPreOrderStatus($orderId)
    {
        $order = Mage::getModel('sales/order')->load($orderId);

        $status = Mage::getModel('sales/order_status')->loadDefaultByState('codeswholesale_preordered_state');
        $status = $status->getStatus();
        $comment = 'Key\'s are PreOrdered by ' . $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
        $order->setStatus($status);
        $order->addStatusHistoryComment($comment, false);
        $order->save();
    }

    /*
     * Sets Status for Failed Process
     */
    public function setFailedStatus($orderId, $error)
    {
        $order = Mage::getModel('sales/order')->load($orderId);

        $status = Mage::getModel('sales/order_status')->loadDefaultByState('codeswholesale_failed_state');
        $status = $status->getStatus();
        $comment = 'Game keys weren\'t sent due to script errors: ' . $error->getMessage();
        $order->setStatus($status);
        $order->addStatusHistoryComment($comment, false);
        $order->save();
    }
}