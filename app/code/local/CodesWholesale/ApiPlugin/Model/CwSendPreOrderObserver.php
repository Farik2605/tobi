<?php

use CodesWholesaleFramework\Postback\ReceivePreOrders\UpdateOrderWithPreOrdersAction;

class CodesWholesale_ApiPlugin_Model_CwSendPreOrderObserver
{
    /**
     * @param $newKeys
     */
    public function sendPreOrderKeys($newKeys)
    {
        try {

            $action = new UpdateOrderWithPreOrdersAction(new CodesWholesale_ApiPlugin_Dispatchers_MageUpdateOrderWithPreOrders());
            $action->setKeys($newKeys);
            $action->process();

        } catch (Exception $e) {

            $mail = new CodesWholesale_ApiPlugin_Mails_MageSendAdminGeneralErrorMail();
            $mail->sendAdminGeneralErrorMail('Issue while receiving PreOrder', $e);
        }
    }
}
