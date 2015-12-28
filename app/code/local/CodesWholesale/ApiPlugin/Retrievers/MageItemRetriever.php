<?php

use CodesWholesaleFramework\Postback\Retriever\ItemRetrieverInterface;

class CodesWholesale_ApiPlugin_Retrievers_MageItemRetriever implements ItemRetrieverInterface
{
    /**
     * @return arrays
     */
    public function retrieveItem($orderId){

        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $productTable = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');

        $query = "SELECT * FROM " . $productTable . " WHERE cw_order_id = '" . $orderId . "'";

        $result = $read->fetchAll($query);

        try {

            $item = Mage::getModel('sales/order_item')->load($result[0]['item_id']);

        } catch (Exception $e) {

           echo 'Caught exception: ' . $e->getMessage();


        }

        return $item;
    }
}