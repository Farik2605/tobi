<?php
require_once 'vendor/autoload.php';
use CodesWholesale\CodesWholesale;

class CodesWholesale_ApiPlugin_IndexController extends Mage_Core_Controller_Front_Action{
    public function indexAction(){
        $params = array(

        );
       /* $client = Mage::helper("apiplugin")->connectToCw();
        foreach($client->getProducts() as $product){

            echo $product->getName()." - ".$product->getStockQuantity();
            //var_dump(get_class_methods($product));
        }
        $collection = Mage::getModel('pin/pin')->getCollection()->addFieldToFilter("product_id",array("eq"=>2));
        echo $collection->getSize();
        foreach($collection as $c){
            echo $c->getProductId()."<br />";
        }*/
        //var_dump(Mage::helper("apiplugin")->getProductById($product->getData("kontenta_corresponding_product")));

        //$product = Mage::getModel("catalog/product")->load(2);
        //var_dump($product->getData());
        //$client = Mage::helper("apiplugin")->connectToCw();
        $product = Mage::getModel("catalog/product")->load(11);
        echo $product->getName();
        $stockItem =Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getEntityId());
        var_dump($stockItem->getData());
        $cart = Mage::getSingleton('checkout/cart');
        $quote = $cart->getQuote();
        echo $quote->getItemsCount()."<br/>";
        foreach($quote->getItemsCollection() as $item){
            echo $item->getProductId()." ";
        }
    }
} 