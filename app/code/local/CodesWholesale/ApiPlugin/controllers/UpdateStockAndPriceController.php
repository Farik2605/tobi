<?php

require_once 'vendor/autoload.php';

use CodesWholesaleFramework\Postback\UpdatePriceAndStock\UpdatePriceAndStockAction;


class CodesWholesale_ApiPlugin_UpdateStockAndPriceController extends Mage_Core_Controller_Front_Action
{
    public function updateStockAction()
    {
        $action = new UpdatePriceAndStockAction(
            new CodesWholesale_ApiPlugin_Update_MageUpdatePriceAndStock(),
            new CodesWholesale_ApiPlugin_Retrievers_MageSpreadRetriever());

        $action->setProductId($cwProductId = null);
        $action->setConnection(Mage::helper('apiplugin/data')->connectToCw());
        $action->process();

    }
}