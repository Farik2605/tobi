<?php

require_once 'vendor/autoload.php';
use CodesWholesaleFramework\Orders\Codes\OrderCreatorAction;

class CodesWholesale_ApiPlugin_Model_CwOrderObserver
{
    static protected $_singletonFlag = false;
    /**
     * This method checks for complete transaction. If Transaction is completed plugin sends
     * CodesWholesale Game Key's to customer. Also included another statuses like PreOrdered by CodesWholesale or
     * Failed by CodesWholesale.
     */
    public function statusChange($observer)
    {
        if (!self::$_singletonFlag) {
            self::$_singletonFlag = true;

            $action = new OrderCreatorAction(
                new CodesWholesale_ApiPlugin_Creators_MageStatusChangeChecker(),
                new CodesWholesale_ApiPlugin_Exporters_MageDataBaseExporter(),
                new CodesWholesale_ApiPlugin_Dispatchers_MageOrderEventDispatcher(),
                new CodesWholesale_ApiPlugin_Retrievers_MageOrderItemRetriever(),
                new CodesWholesale_ApiPlugin_Mails_MageSendAdminErrorMail(),
                new CodesWholesale_ApiPlugin_Mails_MageSendAdminGeneralErrorMail(),
                new CodesWholesale_ApiPlugin_Validators_MageOrderValidate());

            $action->setConnection(Mage::helper('apiplugin/data')->connectToCw());
            $action->setCurrentStatus($observer);
            $action->process();

        }
    }
}
