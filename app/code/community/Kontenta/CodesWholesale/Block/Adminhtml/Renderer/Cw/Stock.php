<?php
class Kontenta_CodesWholesale_Block_Adminhtml_Renderer_Cw_Stock extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    public function render(Varien_Object $row){
        $collection = Mage::getModel('pin/pin')->getCollection()->addFieldToFilter("product_id",array("eq"=>$row->getEntityId()));
        return $collection->getSize() ? $collection->getSize() : '0';
    }
}