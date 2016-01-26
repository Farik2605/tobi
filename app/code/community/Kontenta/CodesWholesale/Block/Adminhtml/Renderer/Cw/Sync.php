<?php
class Kontenta_CodesWholesale_Block_Adminhtml_Renderer_Cw_Sync extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    public function render(Varien_Object $row){
        if($row->getData(Kontenta_CodesWholesale_Model_Product::CW_SYNC_STOCK) == '1'){
            $product = Mage::helper("kontentaCw")->getRendererData($row);
            if($product){
                if($product["qty"])
                    return $product["qty"];
                else
                    return "0";
            }
            return "None";
        }else
            return "None";
    }
} 