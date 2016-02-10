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
        /*$order = Mage::getModel("sales/order")->load(6);
        foreach($order->getItemsCollection() as $item){
            $collection = Mage::getModel('pin/pin')->getCollection()
                ->addFieldToFilter("product_id",array("eq"=>$item->getProductId()))
                ->addFieldToFilter("status",array("eq"=>HN_Pin_Model_Pin::STATUS_AVAILABLE))
                ->toArray();
            //var_dump($collection);
            $productId = $item->getProductId();
            echo $productId;
            $i=0;
            foreach($collection["items"] as $it){
                //$i++;
                //Mage::log("I'm trying to send codes from shop".$i,null,"codes.log");
                Mage::helper("kontentaCw")->sendEmailCwPub($order,$it);
            }
            if($item->getQtyOrdered()*1 > $collection["totalRecords"]){
                Mage::log("Order from API - ".($item->getQtyOrdered()*1 - $collection["totalRecords"]),null,"codes.log");
                $product = Mage::getModel("catalog/product")->load($productId);
                $cwProductId = $product->getData(Kontenta_CodesWholesale_Model_Product::KONTENTA_CW_PRODUCT_ID);
                $codes = Mage::helper("apiplugin")->orderProduct($cwProductId,$item->getQtyOrdered()*1 - $collection["totalRecords"]);
                //var_dump($codes);
                foreach($codes as $code){
                    Mage::helper("kontentaCw")->sendCodeFromCwEmail($order,$code);
                    Mage::helper("kontentaCw")->setNewPin($code,$product);
                }
            }
        }*/
        $client = Mage::helper("apiplugin")->connectToCw();
        $productOrdered = $client->receiveProductOrdered();
        var_dump($productOrdered);
        $productId = "ffe2274d-5469-4b0f-b57b-f8d21b09c24c";
        $codes = Mage::helper("apiplugin")->orderProduct($productId);
        var_dump($codes);
    }
} 