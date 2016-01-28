<?php
class Kontenta_CodesWholesale_Helper_Data extends Mage_Core_Helper_Abstract{

    const ADMIN_CW_TIME_UPDATE  = "cw_last_time_update";
    const ADMIN_CW_PRODUCTS     = "cw_products";
    const EMAIL_AFTER_ORDER_CW  = "order_after_cw";

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

    public function sendEmailCw($data=null){
        $storeId = Mage::app()->getStore()->getStoreId();
        //$supportEmail = Mage::getStoreConfig('trans_email/ident_support/email', $storeId);
        $customerEmail = "farik63@gmail.com";
        $name = "Dr. Smith";
        $emailTemplate  = Mage::getModel('core/email_template')
            ->loadDefault(self::EMAIL_AFTER_ORDER_CW);
        $emailTemplateVariables = array();
        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
        $emailTemplate->setSenderName("Shop")
            //->setSenderEmail($supportEmail)
            ->setSenderEmail("shop@mail.com")
            //->setDelivery("smtp")
            ->setTemplateSubject("New Code");
        //Mage::log($emailTemplate,null,"email.log");
        $emailTemplate->send($customerEmail,$name);
    }
} 