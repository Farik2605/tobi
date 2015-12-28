<?php
require_once 'vendor/autoload.php';

class CodesWholesale_ApiPlugin_Block_Adminhtml_Catalog_Product_Tab extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    public function _construct()
    {
        parent::_construct();

        $this->setTemplate('apiplugin/catalog/product/tab.phtml');
    }

    public function getTabLabel()
    {
        return $this->__('CodesWholesale Product Settings');
    }

    public function getTabTitle()
    {
        return $this->__('Click here to view your custom tab content');
    }

    public function canShowTab()
    {
        $product = Mage::registry('product');

        if (!($setId = $product->getAttributeSetId())) {
            $setId = $this->getRequest()->getParam('set', null);
        }
        if ($setId) {
            return true;
        }
        return false;
    }

    public function isHidden()
    {
        return false;
    }

    public function getProducts() {

        return Mage::helper('apiplugin/data')->connectToCw()->getProducts();
    }
    public function getProductOptions()
    {
        $productId = Mage::registry('current_product')->getId();
        $storeId   = Mage::app()->getStore()->getStoreId();

        $cwProductOptions = array(

            $cw_product_id_value =  Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, 'codeswholesale_product_id', $storeId),
            $cw_calculate_type   =  Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, 'codeswholesale_calculate_value', $storeId),
        );

        return $cwProductOptions;
    }
}