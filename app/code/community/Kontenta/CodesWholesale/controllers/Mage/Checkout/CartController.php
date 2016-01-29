<?php
$path = Mage::getBaseDir()
    . DS . 'app' . DS . 'code' . DS . 'core'
    . DS . 'Mage' . DS . 'Checkout' . DS . 'controllers'
    . DS . 'CartController.php';
require_once $path;
class Kontenta_CodesWholesale_Mage_Checkout_CartController extends Mage_Checkout_CartController{
    public function indexAction(){
        $cart = $this->_getCart();
        if(Mage::helper("apiplugin")->checkPreOrder()){
            $warning = "It is not possible to preorder and buy already released products in the same order. Pleace place 2 orders.";
            $cart->getCheckoutSession()->addNotice($warning);
        }
        parent::indexAction();
    }
}