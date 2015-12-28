<?php
use CodesWholesaleFramework\Orders\Utils\OrderValidationInterface;

class CodesWholesale_ApiPlugin_Validators_MageOrderValidate implements OrderValidationInterface
{

    public function validatePurchase($orderedCodes, $item, $orderDetails, $connection, $error)
    {

        $account = $connection->getAccount();

        if (doubleval(Mage::getStoreConfig('apiplugin_connection/apiplugin_group/apiplugin_balance')) >= doubleval($account->getCurrentBalance())) {

            $mail = new CodesWholesale_ApiPlugin_Mails_MageNotifyLowBalance();
            $mail->notifyLowBalance($orderDetails['order']);
        }

        if (!$error) {

            if ($orderedCodes['preOrders'] > 0) {

                $gameNames[] = $item->getData('name');

                $mail = new CodesWholesale_ApiPlugin_Mails_MageNotifyAboutPreOrder();
                $mail->notifyAboutPreOrder($orderDetails['order'], $gameNames, $orderedCodes['preOrders']);

                $orderedCodes['preOrders'] = 0;

                $status = new CodesWholesale_ApiPlugin_Statuses_MageSetPreOrderStatus();
                $status->setStatus($orderDetails['orderId']);
            }

            $eventDataArray = array('order' => $orderDetails['order']);
            return $eventDataArray;
        }
    }
}