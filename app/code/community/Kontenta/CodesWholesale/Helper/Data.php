<?php
class Kontenta_CodesWholesale_Helper_Data extends Mage_Core_Helper_Abstract{

    const ADMIN_CW_TIME_UPDATE          = "cw_last_time_update";
    const ADMIN_CW_PRODUCTS             = "cw_products";
    const EMAIL_AFTER_ORDER_CW          = "order_after_cw";
    const EMAIL_AFTER_ORDER_CW_FILE     = "order_after_cw_file";
    const EMAIL_CW_CODE_ERROR           = "order_cw_error_code";

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

    public function sendEmailCwPub($order,$it){
        $this->sendEmailCw($order,$it["pin_number"],$it["fileblob"],$it["file"]);
        $item1 = Mage::getModel("pin/pin")->load($it["id"])->setStatus(HN_Pin_Model_Pin::STATUS_SOLD_OUT)->save();
    }

    public function sendEmailCw($order,$pin_number="",$fileblob="",$filename=""){
        $storeId = Mage::app()->getStore()->getStoreId();
        //$supportEmail = Mage::getStoreConfig('trans_email/ident_support/email', $storeId);
        $customerEmail = $order->getCustomerEmail();
        $name = "Dr. Smith";
        if($fileblob){
            $emailTemplate  = Mage::getModel('core/email_template')
                ->loadDefault(self::EMAIL_AFTER_ORDER_CW_FILE);
            $emailTemplateVariables = array("order"=>$order);
        }else{
            $emailTemplate  = Mage::getModel('core/email_template')
                ->loadDefault(self::EMAIL_AFTER_ORDER_CW);
            $emailTemplateVariables = array("order"=>$order,"filetext"=>Mage::helper('core')->decrypt($pin_number));
        }
        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
        $from_name = Mage::getStoreConfig('trans_email/ident_general/name');
        $from_email = Mage::getStoreConfig('trans_email/ident_general/email');
        $emailTemplate->setSenderName($from_name)
            ->setSenderEmail($from_email)
            ->setTemplateSubject("New Code");
        if($fileblob)
            $emailTemplate->getMail()->createAttachment(
                $fileblob,
                Zend_Mime::TYPE_OCTETSTREAM,
                Zend_Mime::DISPOSITION_ATTACHMENT,
                Zend_Mime::ENCODING_BASE64,
                $filename
            );
        $emailTemplate->send($customerEmail,$name);
    }

    public function sentErrorEmail($e, $product, $qty=1){
        //get admin email
        $adminEmail = Mage::getStoreConfig('trans_email/ident_support/email');
        $emailTemplate  = Mage::getModel('core/email_template')
            ->loadDefault(self::EMAIL_CW_CODE_ERROR);
        $emailTemplateVariables = array("exception"=>$e->getMessage(),"product"=>$product->getName(),"qty"=>$qty);
        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
        $from_name = Mage::getStoreConfig('trans_email/ident_general/name');
        $from_email = Mage::getStoreConfig('trans_email/ident_general/email');
        $emailTemplate->setSenderName($from_name)
            ->setSenderEmail($from_email)
            ->setTemplateSubject("Error while ordering cw code.");
        $emailTemplate->send($adminEmail,"");
    }

    public function sendCodeFromCwEmail($order,$code){
        if($code->isText()) {
            $this->sendEmailCw($order,Mage::helper('core')->encrypt($code->getCode()));
        }
        if($code->isImage()) {
            $this->sendEmailCw($order,"",$code->getCode(),$code->getFileName());
        }
    }

    public function setNewPin($code,$product){
        $pin = Mage::getModel("pin/pin")
            ->setProductId($product->getEntityId())
            ->setProductName($product->getName())
            ->setStatus(HN_Pin_Model_Pin::STATUS_SOLD_OUT);
        if($code->isText()) {
            $pin->setPinNumber(Mage::helper('core')->encrypt($code->getCode()));
            $pin->setFiletype("encryptedtext");
        }
        if($code->isImage()) {
            $pin->setFileblob($code->getCode());
            $pin->setFile($code->getFileName());
        }
        $pin->save();
    }

    public function synchProductQty($product){
       $this->_synchProd($product->getEntityId());
    }

    public function synchProductIdQty($productId){
        return $this->_synchProd($productId);
    }

    protected function _synchProd($productId){
        $product = Mage::getModel("kontentacw/product")->load($productId);
        $product->synchronize();
        return $product;
    }
} 