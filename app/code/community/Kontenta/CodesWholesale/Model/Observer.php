<?php
class Kontenta_CodesWholesale_Model_Observer {

    public $attribute = Kontenta_CodesWholesale_Model_Product::CW_SYNC_PRICE;

    public function addFlagColumnToProductGrid(Varien_Event_Observer $observer) {
        $block = $observer->getEvent()->getBlock();
        if (!isset($block)) return;

        if ($block->getType() == 'adminhtml/catalog_product_grid'){
            $array = array(
                '0' => "No",
                '1' => "Yes"
            );
            $block->addColumnAfter(
                'kontenta_cw_sync_price',
                array(
                    'header' => Mage::helper("adminhtml")->__('CW price sync'),
                    'align' => 'center',
                    'width' => '47px',
                    'type' => 'options',
                    'options' => $array,
                    'index' => $this->attribute,
                ),
                'sku'
            );
            $block->sortColumnsByOrder();
        }
    }

    public function onEavLoadBeforeProductFlag(Varien_Event_Observer $observer) {
        $collection = $observer->getCollection();
        if(get_class($collection) == "Mage_Catalog_Model_Resource_Product_Collection" or get_parent_class($collection) == "Mage_Catalog_Model_Resource_Product_Collection"){
            $collection->joinAttribute(Kontenta_CodesWholesale_Model_Product::CW_SYNC_PRICE, 'catalog_product/'.Kontenta_CodesWholesale_Model_Product::CW_SYNC_PRICE, 'entity_id', null, 'left');
        }
    }
} 