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

    public function orderProduct($productId, $qty=1){
        $client = $this->connectToCw();
        $product = $this->getProductById($productId);
        //example
        $url = "https://sandbox.codeswholesale.com/v1/products/ffe2274d-5469-4b0f-b57b-f8d21b09c24c";
        $url = "https://sandbox.codeswholesale.com/v1/products/04aeaf1e-f7b5-4ba9-ba19-91003a04db0a";
        $url = "https://sandbox.codeswholesale.com/v1/products/6313677f-5219-47e4-a067-7401f55c5a3a";
        //$product = \CodesWholesale\Resource\Product::get($url);
        try{
            $codes = \CodesWholesale\Resource\Order::createBatchOrder($product, array('quantity' => $qty));
            return $codes;
        }catch(\CodesWholesale\Resource\ResourceError $e){
            //Mage::log($e,null,"except.log");
            Mage::helper("kontentaCw")->sentErrorEmail($e, $product, $qty);
            /*if($e->getStatus() == 400 && $e->getErrorCode() == 10002) {
                // send email
                // log it to database
                echo $e->getMessage();
            } else
                // handle scenario when code details where not found
                if($e->getStatus() == 404 && $e->getErrorCode() == 50002) {
                    // error when image binary data cannot be found within additional request
                    // after order is made this shouldn't occurred
                    echo $e->getMessage();
                } else
                    // handle scenario when product was not found in price list
                    if($e->getStatus() == 404 && $e->getErrorCode() == 20001) {
                        // error can occurred when you present e.g. some old products that are now excluded from price list.
                        // redirect user to some error page
                    } else
                        // handle when quantity was less then 1
                        if($e->getStatus() == 400 && $e->getErrorCode() == 40002) {
                            // the input data was not validated on your side and passed to CWS
                            // quantity can't be <= 0
                            echo $e->getMessage();
                        } else {
                            // handle general app error
                            // Log it to database, and give us a shout if it's our false at devteam@codeswholesale.com
                            echo $e->getCode();
                            echo $e->getErrorCode();
                            echo $e->getMoreInfo();
                            echo $e->getDeveloperMessage();
                            echo $e->getMessage();
                        }*/
            return null;
        }
    }
}
	 