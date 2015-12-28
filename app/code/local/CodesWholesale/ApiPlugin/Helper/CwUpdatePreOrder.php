<?php

class CodesWholesale_ApiPlugin_Helper_CwUpdatePreOrder extends Mage_Core_Helper_Abstract
{

    public function updateLinks($orderId, $links){

        $write = Mage::getSingleton( 'core/resource' )->getConnection( 'core_write' );
        $productTable = Mage::getSingleton( 'core/resource' )->getTableName( 'sales_flat_order_item' );

        $query = "UPDATE " . $productTable . " SET links = '$links' WHERE order_id = '$orderId'";

        return $write->query($query);
    }

    public function updatePreOrder($orderId, $preOrdersLeft){

        $write = Mage::getSingleton( 'core/resource' )->getConnection( 'core_write' );
        $productTable = Mage::getSingleton( 'core/resource' )->getTableName( 'sales_flat_order_item' );

        $query = "UPDATE " . $productTable . " SET number_of_preorder = '$preOrdersLeft' WHERE order_id = '$orderId'";

        return $write->query($query);
    }
}