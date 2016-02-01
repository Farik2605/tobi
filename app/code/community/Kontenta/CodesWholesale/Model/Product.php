<?php
class Kontenta_CodesWholesale_Model_Product extends Mage_Catalog_Model_Product{
    const KONTENTA_CW_PRODUCT_ID    = "kontenta_corresponding_product";
    const CW_SYNC_PRICE             = "kontenta_cw_sync_price";
    const CW_SYNC_STOCK             = "kontenta_cw_sync_stock";
    const CW_IF_PREORDER            = "kontenta_cw_preorder";

    protected $_productCW;

    public function synchronize(){
        if($this->getData(self::KONTENTA_CW_PRODUCT_ID)){
            if($this->getData(self::CW_SYNC_PRICE))
                $this->_syncCost();
        }
        $this->_syncStock();
    }

    protected function _syncCost(){
        $productCW = $this->_getProductCW();
        if($productCW)
            $this->setCost($productCW->getDefaultPrice());
        return $this;
    }

    protected function _syncStock(){
        $collection = Mage::getModel('pin/pin')->getCollection()
            ->addFieldToFilter("product_id",array("eq"=>$this->getEntityId()))
            ->addFieldToFilter("status",array("eq"=>HN_Pin_Model_Pin::STATUS_AVAILABLE));
        $qty = $collection->getSize() ? $collection->getSize() : '0';
        $qty = $qty*1;
        if($this->getData(self::CW_SYNC_STOCK)){
            $product = Mage::helper("kontentaCw")->getRendererData($this);
            if($product)
                $qty += $product["qty"];
        }
        $stockItem =Mage::getModel('cataloginventory/stock_item')->loadByProduct($this->getEntityId());
        $stockItem->setData('qty', $qty);
        $stockItem->save();
        return $this;
    }

    protected function _getProductCW(){
        if(!$this->_productCW){
            $this->_productCW = Mage::helper("apiplugin")->getProductById($this->getData(self::KONTENTA_CW_PRODUCT_ID));
        }
        return $this->_productCW;
    }
} 