<?php

class CodesWholesale_ApiPlugin_Model_Observer
{
    static protected $_singletonFlag = false;

    public function saveProductTabData($observer)
    {
        if (!self::$_singletonFlag) {
            self::$_singletonFlag = true;

            $product = $observer->getEvent()->getProduct();

            $cwProductId = $this->_getRequest()->getPost('codeswholesale_product_id');
            $cwCalculate = $this->_getRequest()->getPost('codeswholesale_calculate_value');

            $product->setData('codeswholesale_product_id', $cwProductId)->getResource()->saveAttribute($product, 'codeswholesale_product_id');
            $product->setData('codeswholesale_calculate_value', $cwCalculate)->getResource()->saveAttribute($product, 'codeswholesale_calculate_value');
        }
    }

    public function getProduct()
    {
        return Mage::registry('product');
    }

    protected function _getRequest()
    {
        return Mage::app()->getRequest();
    }

}