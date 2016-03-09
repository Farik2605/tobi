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
        foreach($order->getItemsCollection() as $item){
            $productId = $item->getProductId();
            $product = Mage::getModel("catalog/product")->load($productId);
            $cwProductId = $product->getData(Kontenta_CodesWholesale_Model_Product::KONTENTA_CW_PRODUCT_ID);
            if($cwProductId){
                $collection = Mage::getModel('pin/pin')->getCollection()
                    ->addFieldToFilter("product_id",array("eq"=>$item->getProductId()))
                    ->addFieldToFilter("status",array("eq"=>HN_Pin_Model_Pin::STATUS_AVAILABLE))
                    ->toArray();
                //Cut array to the qty ordered
                $resultArray = array_slice($collection["items"],0,$item->getQtyOrdered()*1);
                foreach($resultArray as $it){
                    Mage::helper("kontentaCw")->sendEmailCwPub($order,$it);
                }
                if($item->getQtyOrdered()*1 > $collection["totalRecords"]){
                    $codes = Mage::helper("apiplugin")->orderProduct($cwProductId,$item->getQtyOrdered()*1 - $collection["totalRecords"]);
                    foreach($codes as $code){
                        Mage::helper("kontentaCw")->sendCodeFromCwEmail($order,$code);
                        Mage::helper("kontentaCw")->setNewPin($code,$product);
                    }
                }
                Mage::helper("kontentaCw")->synchProductIdQty($productId);
            }
        }
   }

    public function onSaveProductAfter($observer){
        $product = $observer->getProduct();
        Mage::helper("kontentaCw")->synchProductQty($product);
    }
} 