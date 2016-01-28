<?php
class Kontenta_CodesWholesale_Model_Observer {

    public $attribute   = Kontenta_CodesWholesale_Model_Product::CW_SYNC_PRICE;
    public $attribute2  = Kontenta_CodesWholesale_Model_Product::CW_SYNC_STOCK;

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
                    'header' => Mage::helper("adminhtml")->__('CW cost price sync'),
                    'align' => 'center',
                    'width' => '20px',
                    'type' => 'options',
                    'options' => $array,
                    'index' => $this->attribute,
                ),
                'sku'
            );
            $block->addColumnAfter(
                'kontenta_cw_sync_stock',
                array(
                    'header' => Mage::helper("adminhtml")->__('CW Sync'),
                    'align' => 'center',
                    'width' => '20px',
                    'type' => 'options',
                    'options' => $array,
                    'index' => $this->attribute2,
                ),
                'kontenta_cw_sync_price'
            );
            $block->addColumnAfter(
                'kontenta_cw_sync_stock2',
                array(
                    'header' => Mage::helper("adminhtml")->__('CW Stock'),
                    'align' => 'center',
                    'width' => '47px',
                    'type' => 'text',
                    'index' => 'entity_id',
                    'renderer'  => new Kontenta_CodesWholesale_Block_Adminhtml_Renderer_Cw_Sync(),
                ),
                'kontenta_cw_sync_stock'
            );
            $block->addColumnAfter(
                'kontenta_cw_sync_store_stock',
                array(
                    'header' => Mage::helper("adminhtml")->__('Store Stock'),
                    'align' => 'center',
                    'width' => '47px',
                    'type' => 'text',
                    'index' => 'entity_id',
                    'renderer'  => new Kontenta_CodesWholesale_Block_Adminhtml_Renderer_Cw_Stock(),
                ),
                'kontenta_cw_sync_stock2'
            );
            $block->sortColumnsByOrder();
        }
    }

    public function onEavLoadBeforeProductFlag(Varien_Event_Observer $observer) {
        $collection = $observer->getCollection();
        if(get_class($collection) == "Mage_Catalog_Model_Resource_Product_Collection" or get_parent_class($collection) == "Mage_Catalog_Model_Resource_Product_Collection"){
            $collection->joinAttribute(Kontenta_CodesWholesale_Model_Product::CW_SYNC_PRICE, 'catalog_product/'.Kontenta_CodesWholesale_Model_Product::CW_SYNC_PRICE, 'entity_id', null, 'left');
            $collection->joinAttribute(Kontenta_CodesWholesale_Model_Product::CW_SYNC_STOCK, 'catalog_product/'.Kontenta_CodesWholesale_Model_Product::CW_SYNC_STOCK, 'entity_id', null, 'left');
            $collection->joinAttribute(Kontenta_CodesWholesale_Model_Product::KONTENTA_CW_PRODUCT_ID, 'catalog_product/'.Kontenta_CodesWholesale_Model_Product::KONTENTA_CW_PRODUCT_ID, 'entity_id', null, 'left');
        }
    }

   public function placeOrderAfter($observer){
        $order = $observer->getEvent()->getOrder();
        $customerEmail = $order->getCustomerEmail();
        foreach($order->getItemsCollection() as $item){
            for($i=0; $i < $item->getQtyOrdered()*1; $i++)
                Mage::helper("kontentaCw")->sendEmailCw($customerEmail);
        }
   }
} 