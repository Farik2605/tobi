<?php
/**
 * Created by PhpStorm.
 * User: Fara
 * Date: 29.01.16
 * Time: 16:32
 */

class HN_Pin_Block_Adminhtml_Pin_Notsynced extends Mage_Adminhtml_Block_Widget{
    public function getAllProducts(){
        $products = array();
        foreach(Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter(Kontenta_CodesWholesale_Model_Product::KONTENTA_CW_PRODUCT_ID,array("neq"=>NULL)) as $p)
            $products[] = $p->getData(Kontenta_CodesWholesale_Model_Product::KONTENTA_CW_PRODUCT_ID);
        return $products;
    }

    public function getCwProducts(){
        $client = Mage::helper("apiplugin")->connectToCw();
        return $client->getProducts();
    }

    public function getProducts(){
        $result = array();
        $products = $this->getAllProducts();
        foreach($this->getCwProducts() as $product){
            if(!in_array($product->getProductId(), $products))
                $result[] = $product;
        }
        return $result;
    }
}