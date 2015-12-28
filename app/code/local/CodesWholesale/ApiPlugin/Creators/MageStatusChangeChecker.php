<?php
use CodesWholesaleFramework\Orders\Utils\StatusChangeInterface;

class CodesWholesale_ApiPlugin_Creators_MageStatusChangeChecker implements StatusChangeInterface
{
    public function checkStatus($observer)
    {
        $order = $observer->getEvent()->getOrder();

        $orderId = $order->getId();
        $orderIncrementId = $order->getIncrementId();
        $allStatuses = $order->getAllStatusHistory();

        foreach ($allStatuses as $status) {

            $historyStatus = $status->getData('status');

            if ($historyStatus == 'codeswholesale_completed') {

               // return;
            }
        }
        $observerArray = array();

        if ($order->getState() == Mage_Sales_Model_Order::STATE_COMPLETE) {

            $error = null;
            $orderedItems = $order->getAllVisibleItems();

            $observerArray = array(
                'order' => $order,
                'orderId' => $orderId,
                'orderIncrementId' => $orderIncrementId,
                'orderedItems' => $orderedItems
            );
        }
        return $observerArray;
    }
}