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
}
	 