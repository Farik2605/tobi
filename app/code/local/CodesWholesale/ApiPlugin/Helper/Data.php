<?php
require_once 'vendor/autoload.php';
use CodesWholesaleFramework\Connection\Connection;

class CodesWholesale_ApiPlugin_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function connectToCw()
    {
        if(Connection::hasConnection()) {
            return Connection::getConnection(array());
        }

        $config  = Mage::getConfig()->getResourceConnectionConfig("default_setup")->asArray();

        $pdo = new PDO('mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'], $config['username'], $config['password']);

        $environment = Mage::getStoreConfig ( 'pin/cw_settings/cw_environment');
        if($environment)
            $options = array(
                'environment'   => $environment,
                'client_id'     => Mage::getStoreConfig ( 'pin/cw_settings/cw_client_id'),
                'client_secret' => Mage::getStoreConfig ( 'pin/cw_settings/cw_client_secret'),
                'client_headers' => 'CodesWholesale-Magento/2.0',
                'db' => $pdo
            );
        else
            $options = array(
                'environment'   => $environment,
                'client_id'     => 0,
                'client_secret' => 0,
                'client_headers' => 'CodesWholesale-Magento/2.0',
                'db' => $pdo
            );

        return Connection::getConnection($options);
    }

    public function getProductsBySubStr($substr){
        $client = $this->connectToCw();
        $result = array();
        foreach($client->getProducts() as $product){
            if(stristr($product->getName(), $substr)){
                $result[] = array("id"=>$product->getProductId(),"name"=>$product->getName());
            }
        }
        return $result;
    }

    public function getProductById($id){
        $client = $this->connectToCw();
        foreach($client->getProducts() as $product){
            if($product->getProductId() == $id)
                return $product;
        }
        return false;
    }

    public function checkPreOrder(){
        $cart = Mage::getSingleton('checkout/cart');
        $quote = $cart->getQuote();
        $is_preorder = false;
        $is_normal = false;
        if($quote->getItemsCount() > 1){
            foreach($cart->getQuote()->getItemsCollection() as $item){
                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($item->getProductId());
                if($stockItem->getBackorders() == 1)
                    $is_preorder = true;
                else
                    $is_normal = true;
            }
            return $is_preorder && $is_normal;
        }
        return false;
    }
}
	 