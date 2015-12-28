<?php

require_once 'vendor/autoload.php';

use CodesWholesaleFramework\Orders\Codes\SendCodesAction;

class CodesWholesale_ApiPlugin_Model_CwSendKeysObserver
{

    public function sendKeys($observer)
    {

        $action = new SendCodesAction(
            new CodesWholesale_ApiPlugin_Dispatchers_MageSendCodesObserverDispatcher(),
            new CodesWholesale_ApiPlugin_Mails_MageSendCodeMail(),
            new CodesWholesale_ApiPlugin_Statuses_MageSetCompleteStatus(),
            new CodesWholesale_ApiPlugin_Retrievers_MageLinksRetriever());
        $action->setOrderDetails($observer);
        $action->process();
    }
}



