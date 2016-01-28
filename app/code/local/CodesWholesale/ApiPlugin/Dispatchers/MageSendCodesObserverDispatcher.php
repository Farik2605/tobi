<?php

class CodesWholesale_ApiPlugin_Dispatchers_MageSendCodesObserverDispatcher  {

    public function dispatchObserver($observer){

        /*$event = $observer->getEvent();
        $order = $event->getOrder();

        $orderedItems = $order->getAllVisibleItems();
        $orderId = $order->getId();

        $observerParams = array(
            'order'        => $order,
            'orderedItems' => $orderedItems,
            'orderId'      => $orderId
        );*/

        return $observerParams;
    }
}