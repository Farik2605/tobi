<?php
class HN_Pin_Block_Adminhtml_System_Config_Cw_Connect extends Mage_Adminhtml_Block_System_Config_Form_Field{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element){
        $cwClient = Mage::helper('apiplugin')->connectToCw();

        try {

            $account = $cwClient->getAccount();
            return "<span style='color:green'>Connected</span>";

        } catch (\CodesWholesale\Resource\ResourceError $error) {


            return "<span style='color:red'>Failed</span>";
        }

    }
}