<?php
class CodesWholesale_ApiPlugin_IndexController extends Mage_Core_Controller_Front_Action{
    public function indexAction(){
        $params = array(

        );
        $client = Mage::helper("apiplugin")->connectToCw();
        foreach($client->getProducts() as $product){
            /*echo $product->getName();
            echo "<br />";*/
            //echo strstr($product->getName(), "test")."<br />";
            echo $product->getName()." - ".$product->getStockQuantity();
            //var_dump(get_class_methods($product));
        }
        $collection = Mage::getModel('pin/pin')->getCollection()->addFieldToFilter("product_id",array("eq"=>2));
        echo $collection->getSize();
        foreach($collection as $c){
            echo $c->getProductId()."<br />";
        }
        //var_dump($clientBuilder);
        //var_dump(Mage::helper("apiplugin")->getProductsBySubStr("test"));
        echo "something";
        $product = Mage::getModel("kontentacw/product")->load(2);
        //var_dump(Mage::helper("apiplugin")->getProductById($product->getData("kontenta_corresponding_product")));

        //$product = Mage::getModel("catalog/product")->load(2);
        //var_dump($product->getData());
    }
} 