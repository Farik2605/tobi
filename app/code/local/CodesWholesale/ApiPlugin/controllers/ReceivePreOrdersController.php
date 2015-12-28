<?php

require_once 'vendor/autoload.php';

use CodesWholesaleFramework\Postback\ReceivePreOrders\ReceivePreOrdersAction;

class CodesWholesale_ApiPlugin_ReceivePreOrdersController extends Mage_Core_Controller_Front_Action
{
    /**
     * @throws Exception
     */
    public function receivePreOrdersAction()
    {
        try {

            $action = new ReceivePreOrdersAction(
                new CodesWholesale_ApiPlugin_Retrievers_MageItemRetriever(),
                new CodesWholesale_ApiPlugin_Dispatchers_MageEventDispatcher());
            $action->setConnection(Mage::helper('apiplugin/data')->connectToCw());
            $action->process();

        } catch (Exception $e) {

            Mage::helper('apiplugin/CwEmail')->sendAdminGeneralErrorMail('Issue while receiving PreOrder', $e);
            throw $e;
        }
    }
}