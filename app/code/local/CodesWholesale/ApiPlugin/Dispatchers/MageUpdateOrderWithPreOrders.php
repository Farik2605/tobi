<?php

class CodesWholesale_ApiPlugin_Dispatchers_MageUpdateOrderWithPreOrders
{
    public function update($params, $commentText)
    {
        Mage::helper('apiplugin/CwUpdatePreOrder')->updateLinks($params['item']['order_id'], json_encode(array_merge($params['linksToAdd'], array_values($params['links']))));
        Mage::helper('apiplugin/CwUpdatePreOrder')->updatePreOrder($params['item']['order_id'], $params['preOrdersLeft']);

        $keys[] = array(
            'item' => $params['item'],
            'codes' => $params['codes']
        );

        $order = Mage::getSingleton('sales/order')->loadByAttribute('entity_id', $params['item']['order_id']);

        $mail = new \CodesWholesale_ApiPlugin_Mails_MageSendPreOrderMail();
        $mail->sendPreOrderMail($order, $params['attachments'], $keys, $params['preOrdersLeft']);

        $order->addStatusHistoryComment($commentText);
        $order->save();
    }
}