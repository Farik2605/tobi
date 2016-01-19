<?php
class Kontenta_CodesWholesale_Helper_Data extends Mage_Core_Helper_Abstract{

    const ADMIN_CW_TIME_UPDATE  = "cw_last_time_update";
    const ADMIN_CW_PRODUCTS     = "cw_products";

    public function getRendererData($row){
        $products = $this->_getProducts();
        foreach($products as $product){
            if($row->getData(Kontenta_CodesWholesale_Model_Product::KONTENTA_CW_PRODUCT_ID) == $product["id"])
                return $product;
        }
        return null;
    }

    protected function _getProducts(){
        $session = Mage::getSingleton('admin/session');
        if(!$session->getData(self::ADMIN_CW_PRODUCTS) || !$session->getData(self::ADMIN_CW_TIME_UPDATE) || ($session->getData(self::ADMIN_CW_TIME_UPDATE)*1 + 60 < time())){
            $client = Mage::helper("apiplugin")->connectToCw();
            $products = array();
            foreach($client->getProducts() as $product){
                $arr = array("id"=>$product->getProductId(),"qty"=>$product->getStockQuantity());
                $products[] = $arr;
            }
            $session->setCwProducts($products);
            $session->setCwLastTimeUpdate(time());
        }else{
            $products = $session->getData(self::ADMIN_CW_PRODUCTS);
        }
        return $products;
    }
} 