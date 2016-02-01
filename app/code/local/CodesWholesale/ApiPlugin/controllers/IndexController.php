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
        $order = Mage::getModel("sales/order")->load(1);
        foreach($order->getItemsCollection() as $item){
            $collection = Mage::getModel('pin/pin')->getCollection()
                ->addFieldToFilter("product_id",array("eq"=>$item->getProductId()))
                ->addFieldToFilter("status",array("eq"=>HN_Pin_Model_Pin::STATUS_AVAILABLE))
                ->toArray();
            echo var_dump($collection);
            for($i=0; $i < $item->getQtyOrdered()*1; $i++){
                Mage::helper("kontentaCw")->sendEmailCw($order,$collection["items"][$i]);
            }
        }
        echo "Something";
    }
} 