<?php
$path = Mage::getBaseDir()
    . DS . 'app' . DS . 'code' . DS . 'core'
    . DS . 'Mage' . DS . 'Checkout' . DS . 'controllers'
    . DS . 'OnepageController.php';
require_once $path;
class Kontenta_CodesWholesale_Mage_Checkout_OnepageController extends Mage_Checkout_OnepageController{
    public function indexAction(){
        if(Mage::helper("apiplugin")->checkPreOrder()){
            $this->_redirect('checkout/cart');
            return;
        }
        parent::indexAction();
    }
}