<?php

use CodesWholesaleFramework\Postback\Retriever\SpreadRetriever;

class CodesWholesale_ApiPlugin_Retrievers_MageSpreadRetriever implements SpreadRetriever
{
    public function getSpreadParams(){

        $spreadParams = array(
            'cwSpread' => Mage::getStoreConfig('apiplugin_connection/apiplugin_group/apiplugin_spread_value'),
            'cwSpreadType'=> Mage::getStoreConfig('apiplugin_connection/apiplugin_group/apiplugin_spread_type')
        );

        return $spreadParams;
    }

}